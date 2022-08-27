<?php

namespace Smartmage\Inpost\Plugin;

use Smartmage\Inpost\Model\Config\Source\ShippingMethods;
use Magento\Sales\Model\AbstractModel;
use Magento\Shipping\Helper\Data;

class ShippingDataExtended
{

    protected ShippingMethods $shippingMethods;

    public function __construct(
        ShippingMethods $shippingMethods
    ) {
        $this->shippingMethods = $shippingMethods;
    }

    public function afterGetTrackingPopupUrlBySalesModel(
        Data               $subject,
        $result,
        AbstractModel      $model
    ): string {
        $trackNumber = false;
        $allowedMethods = array_merge(
            array_keys($this->shippingMethods::INPOST_MAPPER),
            $this->shippingMethods::INPOST_CARRIER_CODES
        );
        if ($model instanceof \Magento\Sales\Model\Order) {
            $trackNumber = $this->getOrderTrackingNumber($model, $allowedMethods);
        } elseif ($model instanceof \Magento\Sales\Model\Order\Shipment) {
            $trackNumber = $this->getShipmentTrackingNumber($model, $allowedMethods);
        } elseif ($model instanceof \Magento\Sales\Model\Order\Shipment\Track) {
            if (in_array($model->getCarrierCode(), $allowedMethods)) {
                $trackNumber = $model->getTrackNumber();
            }
        }

        if ($trackNumber) {
            return 'https://inpost.pl/sledzenie-przesylek?number=' . $trackNumber;
        }
        return $result;
    }

    public function getOrderTrackingNumber($order, $allowedMethods)
    {
        foreach ($order->getShipmentsCollection() as $shipment) {
            $shipmentTrackingNumber = $this->getShipmentTrackingNumber($shipment, $allowedMethods);
            if ($shipmentTrackingNumber) {
                return $shipmentTrackingNumber;
            }
        }
        return false;
    }

    public function getShipmentTrackingNumber($shipment, $allowedMethods)
    {
        $tracksCollection = $shipment->getTracksCollection();
        foreach ($tracksCollection->getItems() as $track) {
            if (in_array($track->getCarrierCode(), $allowedMethods)) {
                return $track->getTrackNumber();
            }
        }
        return false;
    }
}
