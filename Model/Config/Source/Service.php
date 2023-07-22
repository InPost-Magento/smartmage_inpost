<?php
declare(strict_types=1);

namespace Smartmage\Inpost\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Service implements OptionSourceInterface
{
    private ShippingMethods $shippingMethods;

    public function __construct(
        ShippingMethods $shippingMethods
    ) {
        $this->shippingMethods = $shippingMethods;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray() : array
    {
        $services = [];

        $methods = $this->shippingMethods->toOptionArray();

        foreach ($methods as $method) {
            $services[$method['value']] = ['value' => $method['value'], 'label' => __($method['label'])];
        }

        return $services;
    }

    /**
     * @param $service
     * @return \Magento\Framework\Phrase
     */
    public function getServiceLabel($service)
    {
        $services = $this->toOptionArray();
        return (isset($services[$service])) ? __($services[$service]['label']) : $service;
    }
}
