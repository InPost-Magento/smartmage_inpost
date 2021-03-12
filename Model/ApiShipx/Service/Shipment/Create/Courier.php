<?php

namespace Smartmage\Inpost\Model\ApiShipx\Service\Shipment\Create;

use Smartmage\Inpost\Model\ApiShipx\Service\Shipment\AbstractCreate;

class Courier extends AbstractCreate
{

    public function createBody($data, $order)
    {
        $this->requestBody = [
            "receiver" => [
                "company_name" => $order->getShippingAddress()->getCompany(),
                "first_name" => $order->getCustomerFirstname(),
                "last_name" => $order->getCustomerLastname(),
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

        if ($data['sending_method'] != 'dispatch_order') {
            $this->requestBody['custom_attributes']['dropoff_point'] = $this->configProvider->getConfigData(
                str_replace('_', '/', $data['service']) . '/default_sending_point'
            );
        }

        parent::createBody($data, $order);
    }

    public function setSampleData()
    {
        $this->requestBody = [
            "receiver" => [
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
                ],
            ],
            "insurance" => [
                "amount" => 50,
                "currency" => "PLN"
            ],
            "cod" => [
                "amount" => 50.00,
                "currency" => "PLN"
            ],
            "custom_attributes" => [
                "sending_method" => "dispatch_order",
            ],
            "service" => "inpost_courier_standard",
            "reference" => "Test",
            "comments" => "dowolny komentarz"
        ];
    }
}
