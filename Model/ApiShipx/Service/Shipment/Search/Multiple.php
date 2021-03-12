<?php

namespace Smartmage\Inpost\Model\ApiShipx\Service\Shipment\Search;

use Smartmage\Inpost\Model\ApiShipx\Service\Shipment\AbstractSearch;
use Smartmage\Inpost\Model\ShipmentRepository;

class Multiple extends AbstractSearch
{
    protected $shipmentRepository;

    public function __construct(
        ShipmentRepository $shipmentRepository
    ) {
        $this->shipmentRepository = $shipmentRepository;
    }

    public function getAllShipments()
    {
        $callResult = $this->call();

        if (isset($callResult['items']) && !empty($callResult['items'])) {
            foreach ($callResult['items'] as $item) {
            }
        }
    }
}
