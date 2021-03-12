<?php

namespace Smartmage\Inpost\Model\ApiShipx\Service\Shipment\Create;

use Smartmage\Inpost\Model\ApiShipx\Service\Shipment\AbstractCreate;
use Smartmage\Inpost\Model\ConfigProvider;

class Courier extends AbstractCreate
{
    public function __construct(
        ConfigProvider $configProvider
    ) {
        parent::__construct($configProvider);
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
                    "id" => "small package",
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
                    "is_non_standard" => false
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
    }

    public function createShipment()
    {
        return $this->call($this->sampleBody);
    }
}
