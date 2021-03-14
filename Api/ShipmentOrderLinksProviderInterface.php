<?php
namespace Smartmage\Inpost\Api;

/**
 * Interface ShipmentOrderLinksProviderInterface
 * @package Smartmage\Inpost\Api
 */
interface ShipmentOrderLinksProviderInterface
{
    /**
     * @param $productId
     * @return mixed
     */
    public function getShipments($productId);
}
