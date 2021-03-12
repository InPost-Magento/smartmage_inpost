<?php

namespace Smartmage\Inpost\Model\ApiShipx\Service\Shipment\Create;

use Smartmage\Inpost\Model\ApiShipx\Service\Shipment\AbstractCreate;
use Smartmage\Inpost\Model\Config\Source\ShippingMethods;
use Smartmage\Inpost\Model\ConfigProvider;

class Locker extends AbstractCreate
{

    /**
     * Locker constructor.
     * @param ConfigProvider $configProvider
     * @param ShippingMethods $shippingMethods
     */
    public function __construct(
        ConfigProvider $configProvider,
        ShippingMethods $shippingMethods
    ) {
        parent::__construct($configProvider, $shippingMethods);
    }

    public function createBody($data, $order)
    {
        $this->requestBody = [
            "receiver" => [
                "email" => $order->getCustomerEmail(),
                "phone" => $order->getShippingAddress()->getTelephone(),
            ],
            "parcels" => [
                "template" => $data['size'],
            ],
            "custom_attributes" => [
                "target_point" => $data['target_locker'],
            ],
        ];

        if ($data['sending_method'] != 'dispatch_order') {
            $requestBody['custom_attributes']['dropoff_point'] = $this->configProvider->getConfigData(
                str_replace('_', '/', $data['service']) . '/default_sending_point'
            );
        }

        parent::createBody($data, $order);

        /*$requestBody = [
            "receiver" => [
                "email" => $order->getCustomerEmail(),
                "phone" => $order->getShippingAddress()->getTelephone(),
            ],
            "parcels" => [
                "template" => $data['size']
            ],
            "insurance" => [
                "amount" => $data['insurance'],
                "currency" => "PLN"
            ],
            "cod" => [
                "amount" => $data['cod'],
                "currency" => "PLN"
            ],
            "custom_attributes" => [
                "sending_method" => $data['sending_method'],
                "target_point" => $data['target_locker'],
                "dropoff_point" => $this->configProvider->getConfigData(str_replace('_', '/', $data['service']))
            ],
            "service" => $data['service'],
            "reference" => $data['reference'],
            "comments" => ""
        ];*/

        return $requestBody;
    }
}
