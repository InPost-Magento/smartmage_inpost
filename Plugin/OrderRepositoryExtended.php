<?php

namespace Smartmage\Inpost\Plugin;

use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Smartmage\Inpost\Api\Data\ShipmentOrderLinkInterface;
use Smartmage\Inpost\Api\ShipmentOrderLinksProviderInterface;
use Magento\Framework\EntityManager\EntityManager;

class OrderRepositoryExtended
{
    /**
     * @var OrderExtensionFactory
     */
    protected $orderExtensionFactory;
    protected $shipmentOrderLinksProvider;
    protected $currentOrder;
    protected $entityManager;

    /**
     * @param OrderExtensionFactory $orderExtensionFactory
     */
    public function __construct(
        OrderExtensionFactory $orderExtensionFactory,
        ShipmentOrderLinksProviderInterface $shipmentOrderLinksProvider,
        EntityManager $entityManager
    ) {
        $this->orderExtensionFactory = $orderExtensionFactory;
        $this->shipmentOrderLinksProvider = $shipmentOrderLinksProvider;
        $this->entityManager = $entityManager;
    }

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderInterface $order
     * @return OrderInterface
     */
    public function afterGet(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order
    ): \Magento\Sales\Api\Data\OrderInterface {
        $this->loadExtensionAttributes($order);
        return $order;
    }

    public function afterGetList(
        OrderRepositoryInterface $subject,
        OrderSearchResultInterface $searchResult
    ) {
        foreach ($searchResult->getItems() as $result) {
            $this->loadExtensionAttributes($result);
        }

        return $searchResult;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     */
    protected function loadExtensionAttributes(OrderInterface &$order)
    {
        $orderExtension = $order->getExtensionAttributes();
        if ($orderExtension === null) {
            $orderExtension = $this->orderExtensionFactory->create();
        }

        $inpostLockerId = $order->getData('inpost_locker_id');
        $orderExtension->setInpostLockerId($inpostLockerId);

        $inpostShipmentsId = $this->getInpostShipments($order);
        $orderExtension->setInpostShipmentLinks($inpostShipmentsId);

        $order->setExtensionAttributes($orderExtension);
    }


    /**
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $order
     * @return OrderInterface[]
     */
    public function beforeSave(
        OrderRepositoryInterface $subject,
        OrderInterface $order
    ) {
        $extensionAttributes = $order->getExtensionAttributes() ?: $this->orderExtensionFactory->create();

        if ($extensionAttributes !== null && $extensionAttributes->getInpostLockerId() !== null) {
            $order->setInpostLockerId($extensionAttributes->getInpostLockerId());
            $order->setInpostShipmentLinks($extensionAttributes->getInpostShipmentLinks());
            $this->currentOrder = $order;
        }

        return [$order];
    }

    public function afterSave(
        OrderRepositoryInterface $subject,
        OrderInterface $order
    ) {
        if ($this->currentOrder !== null) {
            $extensionAttributes = $this->currentOrder->getExtensionAttributes();

            if ($extensionAttributes && $extensionAttributes->getInpostShipmentLinks()) {
                $shipmentLinks = $extensionAttributes->getInpostShipmentLinks();
                if (is_array($shipmentLinks)) {
                    /** @var ShipmentOrderLinkInterface $link */
                    foreach ($shipmentLinks as $link) {
                        $link->setIncrementId($order->getIncrementId());
                        $this->entityManager->save($link);
                    }
                }
            }

            $this->currentOrder = null;
        }

        return $order;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return mixed
     */
    public function getInpostShipments(OrderInterface $order)
    {
        return $this->shipmentOrderLinksProvider->getShipments($order->getIncrementId());
    }
}
