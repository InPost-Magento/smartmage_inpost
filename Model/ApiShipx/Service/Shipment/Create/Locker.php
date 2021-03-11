<?php

namespace Smartmage\Inpost\Model\ApiShipx\Service\Shipment\Create;

use \Smartmage\Inpost\{
    Model\ApiShipx\Service\Shipment\AbstractCreate
};

class Locker extends AbstractCreate
{

    public function __construct()
    {
        $this->sampleBody =
            array(
                "receiver" => [
                    "name" => "Name",
                    "company_name" => "Company name",
                    "first_name" => "Jan",
                    "last_name" => "Kowalski",
                    "email" => "receiver@example.com",
                    "phone" => "888000000",
                ],
                "parcels" => [
                    "template" => "small"
                ],
                "insurance" => [
                    "amount" => 25,
                    "currency" => "PLN"
                ],
                "cod" => [
                    "amount" => 12.50,
                    "currency" => "PLN"
                ],
                "custom_attributes" => [
                    "sending_method" => "dispatch_order",
                    "target_point" => "KRA012"
                ],
                "service" => "inpost_locker_standard",
                "reference" => "Test",
                "comments" => "dowolny komentarz"
            );
    }

    public function createShipment ()
    {
        $this->call($this->sampleBody);
    }

}
