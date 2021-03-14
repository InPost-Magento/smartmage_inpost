<?php

namespace Smartmage\Inpost\Model\ApiShipx\Service\Document\Printout;

use Smartmage\Inpost\Model\ApiShipx\CallResult;
use Smartmage\Inpost\Model\ApiShipx\ErrorHandler;
use Smartmage\Inpost\Model\ApiShipx\Service\Document\AbstractPrintout;
use Smartmage\Inpost\Model\Config\Source\LabelFormat;
use Smartmage\Inpost\Model\ConfigProvider;

class DispatchOrders extends AbstractPrintout
{

    public function __construct(
        ConfigProvider $configProvider,
        ErrorHandler $errorHandler
    ) {
        $organizationId = $configProvider->getOrganizationId();
//        v1/organizations/1/dispatch_orders/printouts?shipment_ids[]=123&shipment_ids[]=124&shipment_ids[]=125&
        $this->callUri = 'v1/organizations/' . $organizationId . '/dispatch_orders/printouts';
        $this->successMessage = __('The return labels has been successfully downloaded');
        parent::__construct($configProvider, $errorHandler);
    }

    public function getDispatchOrders($dispatchOrdersData)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/inpost.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $response = $this->call(null, [
//            LabelFormat::STRING_SIZE => $dispatchOrdersData[LabelFormat::STRING_SIZE],
            LabelFormat::STRING_FORMAT => $dispatchOrdersData[LabelFormat::STRING_FORMAT],
            'shipment_ids' => $dispatchOrdersData['ids']
        ]);

        //throw if api fail
        if ($this->callResult[CallResult::STRING_STATUS] != CallResult::STATUS_SUCCESS)
            throw new \Exception(
                $this->callResult[CallResult::STRING_MESSAGE],
                $this->callResult[CallResult::STRING_RESPONSE_CODE]
            );

        //set success message for frontend
        if (
            !isset($this->callResult[CallResult::STRING_MESSAGE]) ||
            empty($this->callResult[CallResult::STRING_MESSAGE]) ||
            is_null($this->callResult[CallResult::STRING_MESSAGE])
        ) {
            $this->callResult[CallResult::STRING_MESSAGE] = $this->successMessage;
        }

        $this->callResult[CallResult::STRING_FILE] = $response;

        return $this->callResult;
    }
}
