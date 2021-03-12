<?php

namespace Smartmage\Inpost\Controller\Adminhtml\Shipments;

use Magento\Backend\App\Action;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Smartmage\Inpost\Model\ApiShipx\Service\Shipment\Create\Courier;
use Smartmage\Inpost\Model\ApiShipx\Service\Shipment\Create\Locker;

class Save extends AbstractSave
{

    protected function processShippment()
    {
        $data = $this->getRequest()->getParams();
        $shipmentClass = $this->classMapper[$data['shipment_fieldset']['service']];
        $shipmentClass->createBody(
            $data['shipment_fieldset'],
            $this->orderRepository->get($data['shipment_fieldset']['order_id'])
        );

        $response = $shipmentClass->createShipment();
    }
}
