<?php

namespace Smartmage\Inpost\Model\ApiShipx\Service\Document\Printout;

use Smartmage\Inpost\Model\ApiShipx\CallResult;
use Smartmage\Inpost\Model\ApiShipx\Service\Document\AbstractPrintout;
use Smartmage\Inpost\Model\ConfigProvider;
use Smartmage\Inpost\Model\ShipmentManagement;
use Smartmage\Inpost\Model\ShipmentRepository;

class Labels extends AbstractPrintout
{

    public function __construct(
        ConfigProvider $configProvider
    ) {
        $this->successMessage = __('The labels has been successfully downloaded');
        parent::__construct($configProvider);
    }

    public function getLabels($labelsData)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/inpost.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $response = $this->call(null, [
            'format' => $this->fileFormat,
            'shipment_ids' => $labelsData['ids']
        ]);

        //throw if api fail
        if ($this->callResult[CallResult::STRING_STATUS] != CallResult::STATUS_SUCCESS)
            throw new \Exception($this->callResult[CallResult::STRING_MESSAGE], $this->callResult[CallResult::STRING_RESPONSE_CODE]);

        //set success message for frontend
        if (
            !isset($this->callResult[CallResult::STRING_MESSAGE]) ||
            empty($this->callResult[CallResult::STRING_MESSAGE]) ||
            is_null($this->callResult[CallResult::STRING_MESSAGE])
        ) {
            $this->callResult[CallResult::STRING_MESSAGE] = $this->successMessage;
        }

        $this->callResult['file'] = $response;

        $logger->info('$this->callResult');
        $logger->info($this->callResult);

        return $this->callResult;
    }
}
