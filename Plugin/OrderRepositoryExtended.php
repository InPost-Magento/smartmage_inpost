<?php

namespace Smartmage\Inpost\Plugin;

use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class OrderRepositoryExtended
{
    /**
     * @var OrderExtensionFactory
     */
    protected $orderExtensionFactory;

    /**
     * @param OrderExtensionFactory $orderExtensionFactory
     */
    public function __construct(
        OrderExtensionFactory $orderExtensionFactory
    ) {
        $this->orderExtensionFactory = $orderExtensionFactory;
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

    protected function loadExtensionAttributes(OrderInterface &$order)
    {
        $orderExtension = $order->getExtensionAttributes();
        if ($orderExtension === null) {
            $orderExtension = $this->orderExtensionFactory->create();
        }

        $inpostLockerId = $order->getData('inpost_locker_id');
        $orderExtension->setInpostLockerId($inpostLockerId);

        $inpostShipmentId = $order->getData('inpost_shipment_id');
        $orderExtension->setInpostShipmentId($inpostShipmentId);

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

}
