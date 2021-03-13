<?php

namespace Smartmage\Inpost\Model\ResourceModel;

use Smartmage\Inpost\Api\Data\ShipmentOrderLinkInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class ShipmentOrderLink
 *
 * @package Smartmage\Inpost\Model\ResourceModel
 */
class ShipmentOrderLink extends AbstractDb
{
    const TABLE_NAME = 'smartmage_inpost_shipment_order_link';
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, ShipmentOrderLinkInterface::LINK_ID);
    }

}
