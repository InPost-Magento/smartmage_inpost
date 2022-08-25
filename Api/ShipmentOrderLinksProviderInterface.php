<?php
namespace Smartmage\Inpost\Api;

/**
 * Interface ShipmentOrderLinksProviderInterface
 */
interface ShipmentOrderLinksProviderInterface
{
    /**
     * @param $productId
     * @return mixed
     */
    public function getShipments($productId);
}
