<?php

namespace Smartmage\Inpost\Model\ApiShipx\Service\Document\Printout;

use Smartmage\Inpost\Model\ApiShipx\CallResult;
use Smartmage\Inpost\Model\ApiShipx\ErrorHandler;
use Smartmage\Inpost\Model\ApiShipx\Service\Document\AbstractPrintout;
use Smartmage\Inpost\Model\Config\Source\LabelFormat;
use Smartmage\Inpost\Model\ConfigProvider;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

class ReturnLabels extends AbstractPrintout
{
    /**
     * @var PsrLoggerInterface
     */
    protected $logger;

    public function __construct(
        PsrLoggerInterface $logger,
        ConfigProvider $configProvider,
        ErrorHandler $errorHandler
    ) {
        $this->logger = $logger;
        $organizationId = $configProvider->getOrganizationId();
        $this->callUri = 'v1/organizations/' . $organizationId . '/shipments/return_labels';
        $this->singleCallUri = 'v1/organizations/' . $organizationId . '/shipments/{id}/return_label';
        $this->massCallUri = 'v1/organizations/' . $organizationId . '/shipments/return_labels';
        $this->successMessage = __('The return labels has been successfully downloaded');
        parent::__construct($logger, $configProvider, $errorHandler);
    }

    public function getLabels($labelsIds, $shipmentServices)
    {
        $shipmentServices = array_unique($shipmentServices);
        $canUseMassLabelsDownload = true;
        // so far return labels does not work for inpostcourier_alcohol service in API
//        foreach($shipmentServices as $shipmentService) {
//            if(in_array($shipmentService,self::MASS_LABELS_NOT_ALLOWED_LIST)) {
//                $canUseMassLabelsDownload = false;
//                break;
//            }
//        }

        $labelFormat = $this->configProvider->getLabelFormat();
        $labelSize = $this->configProvider->getLabelSize();
        $result = [];
        if($canUseMassLabelsDownload) {
            $result['files'][] = $this->getMassLabels([
                'ids' => $labelsIds,
                LabelFormat::STRING_FORMAT => $labelFormat,
                LabelFormat::STRING_SIZE => $labelSize
            ]);
            $result['format'] = count($shipmentServices) > 1 ? LabelFormat::ZIP : $labelFormat;
        } else {
            foreach($labelsIds as $id) {
                $result['files'][] = $this->getLabel([
                    'id' => $id,
                    LabelFormat::STRING_FORMAT => $labelFormat,
                    LabelFormat::STRING_SIZE => $labelSize
                ]);
            }
            $result['format'] = $labelFormat;
        }
        return $result;
    }

    public function getLabel($labelData)
    {
        $this->callUri = str_replace('{id}', $labelData['id'], $this->singleCallUri);
        $response = $this->call(null, [
            LabelFormat::STRING_SIZE => $labelData[LabelFormat::STRING_SIZE],
            LabelFormat::STRING_FORMAT => $labelData[LabelFormat::STRING_FORMAT]
        ]);

        return $this->processCallResult($response);
    }

    public function getMassLabels($labelsData)
    {
        $this->callUri = $this->massCallUri;
        $response = $this->call(null, [
            LabelFormat::STRING_SIZE => $labelsData[LabelFormat::STRING_SIZE],
            LabelFormat::STRING_FORMAT => $labelsData[LabelFormat::STRING_FORMAT],
            'shipment_ids' => $labelsData['ids']
        ]);

        return $this->processCallResult($response);
    }

    protected function processCallResult($response)
    {
        //throw if api fail
        if ($this->callResult[CallResult::STRING_STATUS] != CallResult::STATUS_SUCCESS) {
            throw new \Exception(
                $this->callResult[CallResult::STRING_MESSAGE],
                $this->callResult[CallResult::STRING_RESPONSE_CODE]
            );
        }

        //set success message for frontend
        if (!isset($this->callResult[CallResult::STRING_MESSAGE]) ||
            empty($this->callResult[CallResult::STRING_MESSAGE]) ||
            is_null($this->callResult[CallResult::STRING_MESSAGE])
        ) {
            $this->callResult[CallResult::STRING_MESSAGE] = $this->successMessage;
        }

        $this->callResult[CallResult::STRING_FILE] = $response;

        return $this->callResult;
    }
}
