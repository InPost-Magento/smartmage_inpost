<?php

namespace Smartmage\Inpost\Api;

interface ShipmentManagementInterface
{
    /**
     * @param array $shipmentData
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function addOrUpdate($shipmentData);

}
