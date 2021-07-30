<?php

declare(strict_types=1);

namespace Smartmage\Inpost\Model\Registry;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderInterfaceFactory;

class Order
{
    /**
     * @var OrderInterface
     */
    private $order;

    /**
     * @var OrderInterfaceFactory
     */
    private $orderFactory;

    public function __construct(OrderInterfaceFactory $orderFactory)
    {
        $this->orderFactory = $orderFactory;
    }

    public function set(OrderInterface $order): void
    {
        $this->order = $order;
    }

    public function get(): OrderInterface
    {
        return $this->order ?? $this->createNullOrder();
    }

    private function createNullOrder(): OrderInterface
    {
        return $this->orderFactory->create();
    }
}
