<?php

namespace Smartmage\Inpost\Api;

interface ShipmentManagementInterface
{
    /**
     * @param int $shipmentId
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function add($shipmentId);

    /**
     * @param int $productId
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function remove($shipmentId);

}
