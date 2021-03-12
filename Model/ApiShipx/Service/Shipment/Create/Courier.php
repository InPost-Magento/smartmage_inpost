<?php

namespace Smartmage\Inpost\Model\ApiShipx\Service\Shipment\Create;

use Smartmage\Inpost\Model\ApiShipx\Service\Shipment\AbstractCreate;
use Smartmage\Inpost\Model\Config\Source\ShippingMethods;
use Smartmage\Inpost\Model\ConfigProvider;

class Courier extends AbstractCreate
{
    /**
     * Courier constructor.
     * @param ConfigProvider $configProvider
     * @param ShippingMethods $shippingMethods
     */
    public function __construct(
        ConfigProvider $configProvider,
        ShippingMethods $shippingMethods
    ) {
        $this->sampleBody =
            [
                "receiver" => [
                    "name" => "Name",
                    "company_name" => "Company name",
                    "first_name" => "Jan",
                    "last_name" => "Kowalski",
                    "email" => "receiver@example.com",
                    "phone" => "888000000",
                    "address" => [
                        "street" => "Cybernetyki",
                        "building_number" => "10",
                        "city" => "Warszawa",
                        "post_code" => "02-677",
                        "country_code" => "PL"
                    ]
                ],
                "parcels" => [
                    "dimensions" => [
                        "length" => "80",
                        "width" => "160",
                        "height" => "340",
                        "unit" => "mm"
                    ],
                    "weight" => [
                        "amount" => "1",
                        "unit" => "kg"
                    ]
                ],
                "insurance" => [
                    "amount" => 50,
                    "currency" => "PLN"
                ],
                "cod" => [
                    "amount" => 50.00,
                    "currency" => "PLN"
                ],
                "service" => "inpost_courier_standard",
                "reference" => "Test",
                "comments" => "dowolny komentarz"
            ];
        parent::__construct($configProvider, $shippingMethods);
    }

    public function createBody($data, $order)
    {
        $this->requestBody = [
            "receiver" => [
                "company_name" => $order->getShippingAddress()->getCompany(),
                "first_name" => $order->getCustomerFirstname(),
                "last_name" => $order->getCustomerLastname(),
                "email" => $order->getCustomerEmail(),
                "phone" => $order->getShippingAddress()->getTelephone(),
                "address" => [
                    "street" => "Cybernetyki",
                    "building_number" => "10",
                    "city" => "Warszawa",
                    "post_code" => "02-677",
                    "country_code" => "PL",
                ]
            ],
            "parcels" => [
                "dimensions" => [
                    "length" => "80",
                    "width" => "160",
                    "height" => "340",
                    "unit" => "mm",
                ],
                "weight" => [
                    "amount" => "1",
                    "unit" => $this->configProvider->getShippingConfigData('weight_unit'),
                ]
            ],
        ];

        parent::createBody($data, $order);

        if ($data['sending_method'] != 'dispatch_order') {
            $this->requestBody['custom_attributes']['dropoff_point'] = $this->configProvider->getConfigData(
                str_replace('_', '/', $data['service']) . '/default_sending_point'
            );
        }
    }
}
