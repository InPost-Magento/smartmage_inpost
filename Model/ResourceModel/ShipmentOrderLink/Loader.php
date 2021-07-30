<?php

namespace Smartmage\Inpost\Model\ResourceModel\ShipmentOrderLink;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Smartmage\Inpost\Api\Data\ShipmentOrderLinkInterface;

/**
 * Class Loader
 *
 * @package Smartmage\Inpost\Model\ResourceModel\ShipmentOrderLink
 */
class Loader
{
    /** @var  \Magento\Framework\EntityManager\MetadataPool */
    private $metadataPool;

    /** @var  ResourceConnection\ */
    private $resourceConnection;

    /**
     * Loader constructor.
     * @param \Magento\Framework\EntityManager\MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection
    ) {
        $this->metadataPool = $metadataPool;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param $productId
     * @return array
     * @throws \Exception
     */
    public function getShipmentIdsByIncrementId($incrementId): array
    {
        $metadata = $this->metadataPool->getMetadata(ShipmentOrderLinkInterface::class);
        $connection = $this->resourceConnection->getConnection();

        $select = $connection
            ->select()
            ->from($metadata->getEntityTable(), ShipmentOrderLinkInterface::LINK_ID)
            ->where(ShipmentOrderLinkInterface::INCREMENT_ID . ' = ?', $incrementId);
        $ids = $connection->fetchCol($select);

        return $ids ?: [];
    }
}
