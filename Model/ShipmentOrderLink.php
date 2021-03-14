<?php
declare(strict_types=1);

namespace Smartmage\Inpost\Model;

use Magento\Framework\Model\AbstractModel;
use Smartmage\Inpost\Api\Data\ShipmentOrderLinkInterface;

/**
 * Class ShipmentOrderLink
 * @package Smartmage\Inpost\Model
 */
class ShipmentOrderLink extends AbstractModel implements ShipmentOrderLinkInterface
{
    /** @var  array  */
    private $extenstionAttributes;

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\ShipmentOrderLink::class);
    }

    /**
     * @return array|mixed|string|null
     */
    public function getShipmentId()
    {
        return $this->getData(self::SHIPMENT_ID);
    }

    /**
     * @return array|mixed|null
     */
    public function getIncrementId()
    {
        return $this->getData(self::INCREMENT_ID);
    }

    /**
     * @param $shipmentId
     * @return array|mixed|null
     */
    public function setShipmentId($shipmentId)
    {
        return $this->setData(self::SHIPMENT_ID, $shipmentId);
    }

    /**
     * @param $incrementId
     * @return array|mixed|null
     */
    public function setIncrementId($incrementId)
    {
        return $this->setData(self::INCREMENT_ID, $incrementId);
    }

    /**
     * @return array|int|mixed|null
     */
    public function getLinkId()
    {
        return $this->getData(self::LINK_ID);
    }

    /**
     * @param int $linkId
     * @return \Smartmage\Inpost\Api\Data\ShipmentOrderLinkInterface|\Smartmage\Inpost\Model\ShipmentOrderLink
     */
    public function setLinkId($linkId)
    {
        return $this->setData(self::LINK_ID, $linkId);
    }

    /**
     * @return array|\Smartmage\Inpost\Api\Data\ShipmentOrderLinkInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->extenstionAttributes;
    }

    /**
     * @param \Smartmage\Inpost\Api\Data\ShipmentOrderLinkInterface $extensionAttributes
     * @return $this|\Smartmage\Inpost\Api\Data\ShipmentOrderLinkInterface
     */
    public function setExtensionAttributes(ShipmentOrderLinkInterface $extensionAttributes)
    {
        $this->extenstionAttributes = $extensionAttributes;
        return $this;
    }
}
