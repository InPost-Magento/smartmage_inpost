<?php

namespace Smartmage\Inpost\Plugin;

use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Smartmage\Inpost\Api\ShipmentOrderLinksProviderInterface;

class OrderRepositoryExtended
{
    /**
     * @var OrderExtensionFactory
     */
    protected $orderExtensionFactory;
    protected $shipmentOrderLinksProvider;

    /**
     * @param OrderExtensionFactory $orderExtensionFactory
     */
    public function __construct(
        OrderExtensionFactory $orderExtensionFactory,
        ShipmentOrderLinksProviderInterface $shipmentOrderLinksProvider
    ) {
        $this->orderExtensionFactory = $orderExtensionFactory;
        $this->shipmentOrderLinksProvider = $shipmentOrderLinksProvider;
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
            $order->setInpostShipmentId($extensionAttributes->getInpostShipmentId());
        }

        return [$order];
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
