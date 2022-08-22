<?php
declare(strict_types=1);

namespace Smartmage\Inpost\Model\ShipmentOrderLink;

use Magento\Framework\EntityManager\EntityManager;
use Smartmage\Inpost\Api\ShipmentOrderLinksProviderInterface;
use Smartmage\Inpost\Model\ResourceModel\ShipmentOrderLink\Loader;
use Smartmage\Inpost\Api\Data\ShipmentOrderLinkInterfaceFactory;

/**
 * Class Provider
 * @package Smartmage\Inpost\Model\ShipmentOrderLink
 */
class Provider implements ShipmentOrderLinksProviderInterface
{
    /** @var  EntityManager */
    private $entityManager;

    /** @var  Loader */
    private $loader;

    /** @var  ShipmentOrderLinkInterfaceFactory */
    private $shipmentOrderLinkFactory;

    /**
     * Provider constructor.
     * @param EntityManager $entityManager
     * @param Loader $loader
     */
    public function __construct(
        EntityManager $entityManager,
        Loader $loader,
        ShipmentOrderLinkInterfaceFactory $shipmentOrderLinkFactory
    ) {
        $this->entityManager = $entityManager;
        $this->loader = $loader;
        $this->shipmentOrderLinkFactory = $shipmentOrderLinkFactory;
    }

    /**
     * @param $incrementId
     * @return array
     * @throws \Exception
     */
    public function getShipments($incrementId)
    {
        $shipmentLinks = [];
        $ids = $this->loader->getShipmentIdsByIncrementId($incrementId);

        foreach ($ids as $id) {
            $shipmentLink = $this->shipmentOrderLinkFactory->create();
            $shipmentLinks[] = $this->entityManager->load($shipmentLink, $id);
        }

        return $shipmentLinks;
    }

    /**
     * @param $shipmentId
     * @return mixed
     * @throws \Exception
     */
    public function getOrderIncrementId($shipmentId)
    {
        $ids = $this->loader->getOrderIncrementIdByShipmentId($shipmentId);
        foreach ($ids as $id) {
            return $id;
        }

        return false;
    }
}
