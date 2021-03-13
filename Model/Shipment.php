<?php

namespace Smartmage\Inpost\Model;

use Smartmage\Inpost\Api\Data\ShipmentInterface;
use Magento\Framework\Model\AbstractModel;

class Shipment extends AbstractModel implements ShipmentInterface
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Shipment::class);
    }

    public function getId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    public function getShipmentId()
    {
        return $this->getData(self::SHIPMENT_ID);
    }

    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    public function getService()
    {
        return $this->getData(self::SERVICE);
    }

    public function getShipmentsAttributes()
    {
        return $this->getData(self::SHIPMENT_ATTRIBUTES);
    }

    public function getSendingMethod()
    {
        return $this->getData(self::SENDING_METHOD);
    }

    public function getReceiverData()
    {
        return $this->getData(self::RECEIVER_DATA);
    }

    public function getReference()
    {
        return $this->getData(self::REFERENCE);
    }

    public function getTrackingNumber()
    {
        return $this->getData(self::TRACKING_NUMBER);
    }

    public function getTargetPoint()
    {
        return $this->getData(self::TARGET_POINT);
    }

    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    public function setShipmentId($shipmentId)
    {
        return $this->setData(self::SHIPMENT_ID, $shipmentId);
    }

    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    public function setService($service)
    {
        return $this->setData(self::SERVICE, $service);
    }

    public function setShipmentAttributes($shipmentAttributes)
    {
        return $this->setData(self::SHIPMENT_ATTRIBUTES, $shipmentAttributes);
    }

    public function setSendingMethod($sendingMethod)
    {
        return $this->setData(self::SENDING_METHOD, $sendingMethod);
    }

    public function setReceiverData($receiverData)
    {
        return $this->setData(self::RECEIVER_DATA, $receiverData);
    }

    public function setReference($reference)
    {
        return $this->setData(self::REFERENCE, $reference);
    }

    public function setTrackingNumber($trackingNumber)
    {
        return $this->setData(self::TRACKING_NUMBER, $trackingNumber);
    }

    public function setTargetPoint($targetPoint)
    {
        return $this->setData(self::TARGET_POINT, $targetPoint);
    }

}
