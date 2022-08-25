<?php

namespace Smartmage\Inpost\Model\ResourceModel;

use Smartmage\Inpost\Api\Data\ShipmentInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Shipment extends AbstractDb
{
    const TABLE_NAME = 'smartmage_inpost_shipment';

    /**
     * Core Date
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $coreDate;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $coreDate,
        $connectionName = null
    ) {
        $this->coreDate = $coreDate;
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, ShipmentInterface::ENTITY_ID);
    }
}
