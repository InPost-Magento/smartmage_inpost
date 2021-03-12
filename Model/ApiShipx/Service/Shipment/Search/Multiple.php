<?php

namespace Smartmage\Inpost\Model\ApiShipx\Service\Shipment\Search;

use Smartmage\Inpost\{Model\ApiShipx\Service\Shipment\AbstractSearch, Model\ConfigProvider, Model\ShipmentRepository};

class Multiple extends AbstractSearch
{

    protected $shipmentRepository;

    public function __construct(
        ConfigProvider $configProvider,
        ShipmentRepository $shipmentRepository
    )
    {
        $this->shipmentRepository = $shipmentRepository;
        parent::__construct($configProvider);
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
