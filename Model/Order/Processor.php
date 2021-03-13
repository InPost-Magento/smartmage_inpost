<?php

namespace Smartmage\Inpost\Model\Order;

use Smartmage\Inpost\Model\ConfigProvider;

class Processor
{
    protected $order;

    /**
     * @var ConfigProvider
     */
    protected $configProvider;

    /**
     * Processor constructor.
     * @param ConfigProvider $configProvider
     */
    public function __construct(
        ConfigProvider $configProvider
    ) {
        $this->configProvider = $configProvider;
    }

    public function getOrderWeight()
    {
        $weightAttributeCode = $this->configProvider->getWeightAttributeCode();
        $weight = 0;
        $store = $this->order->getStore();

        foreach ($this->order->getAllVisibleItems() as $item) {
            $product = $item->getProduct();

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

    public function setOrder($order)
    {
        $this->order = $order;
    }
}
