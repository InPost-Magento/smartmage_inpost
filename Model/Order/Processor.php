<?php

namespace Smartmage\Inpost\Model\Order;

use Magento\Catalog\Model\Product\Type;
use Magento\Sales\Api\OrderRepositoryInterface;
use Smartmage\Inpost\Model\ConfigProvider;
use Smartmage\Inpost\Model\Registry\Order as OrderRegistry;

class Processor
{
    protected $order;

    /**
     * @var ConfigProvider
     */
    protected $configProvider;

    /**
     * @var DataStorage
     */
    protected $dataStorage;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var OrderRegistry
     */
    protected $orderRegistry;

    /**
     * Processor constructor.
     * @param ConfigProvider $configProvider
     * @param OrderRegistry $orderRegistry
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        ConfigProvider $configProvider,
        OrderRegistry $orderRegistry,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->configProvider = $configProvider;
        $this->orderRegistry = $orderRegistry;
        $this->orderRepository = $orderRepository;
    }

    public function getOrderWeight()
    {
        $weightAttributeCode = $this->configProvider->getWeightAttributeCode();
        $weight = 0;
        $store = $this->order->getStore();

        foreach ($this->order->getItems() as $item) {
            $product = $item->getProduct();

            if ($product->getTypeId() != Type::TYPE_SIMPLE) {
                continue;
            }

            $productWeight = $product->getResource()->getAttributeRawValue(
                (int)$item->getProductId(),
                (string)$weightAttributeCode,
                (int)$store->getId()
            );

            if (is_array($productWeight)) {
                $productWeight = 0;
            }
            $weight += $productWeight;
        }

        return $weight;
    }

    public function getStreet()
    {
        return $this->order->getShippingAddress()->getStreetLine(1);
    }

    public function getBuildingNumber(): string
    {
        $street = $this->order->getShippingAddress()->getStreet();
        if (isset($street[1])) {
            if (isset($street[2])) {
                return $this->order->getShippingAddress()->getStreetLine(2) .
                    '/' . $this->order->getShippingAddress()->getStreetLine(3);
            }
            return $this->order->getShippingAddress()->getStreetLine(2);
        }

        return $this->order->getShippingAddress()->getStreetLine(1);
    }

    /**
     * @param $order
     * @return $this
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @param $orderId
     * @return mixed
     */
    public function getOrder($orderId)
    {
        $order = $this->orderRegistry->get();
        if ($order->getId()) {
            $this->setOrder($order);
            return $order;
        } else {
            $order = $this->orderRepository->get($orderId);
            $this->orderRegistry->set($order);
            $this->setOrder($order);
            return $order;
        }
    }
}
