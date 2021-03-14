<?php

namespace Smartmage\Inpost\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface ShipmentOrderLinkInterface
 * @package Smartmage\Inpost\Api\Data
 */
interface ShipmentOrderLinkInterface extends ExtensibleDataInterface
{
    const LINK_ID = "link_id";
    const INCREMENT_ID = 'increment_id';
    const SHIPMENT_ID = 'shipment_id';

    /**
     * @return int
     */
    public function getLinkId();

    /**
     * Get shipment Id
     *
     * @return string|null
     */
    public function getShipmentId();

    /**
     * @return mixed
     */
    public function getIncrementId();

    /**
     * @param int $linkId
     * @return self
     */
    public function setLinkId($linkId);

    /**
     * @param $shipmentId
     * @return mixed
     */
    public function setShipmentId($shipmentId);

    /**
     * @param $incrementId
     * @return mixed
     */
    public function setIncrementId($incrementId);

    /**
     * @return \Smartmage\Inpost\Api\Data\ShipmentOrderLinkInterface|null
     */
    public function getExtensionAttributes();

    /**
     * @param \Smartmage\Inpost\Api\Data\ShipmentOrderLinkInterface $extensionAttributes
     * @return self
     */
    public function setExtensionAttributes(\Smartmage\Inpost\Api\Data\ShipmentOrderLinkInterface $extensionAttributes);
}
