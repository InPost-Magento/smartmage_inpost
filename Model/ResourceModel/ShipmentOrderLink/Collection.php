<?php

namespace Smartmage\Inpost\Model\ResourceModel\ShipmentOrderLink;

use Smartmage\Inpost\Api\Data\ShipmentOrderLinkInterface;
use Smartmage\Inpost\Model;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = ShipmentOrderLinkInterface::LINK_ID;

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Model\ShipmentOrderLink::class, Model\ResourceModel\ShipmentOrderLink::class);
    }
}
