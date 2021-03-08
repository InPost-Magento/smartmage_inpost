<?php

namespace Smartmage\Inpost\Model\ResourceModel\Shipment;

use Smartmage\Inpost\Api\Data\ShipmentInterface;
use Smartmage\Inpost\Model;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 *
 * @package Smartmage\Inpost\Model\ResourceModel\Shipment
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = ShipmentInterface::ENTITY_ID;

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Model\Shipment::class, Model\ResourceModel\Shipment::class);
    }
}
