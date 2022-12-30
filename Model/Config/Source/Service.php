<?php
declare(strict_types=1);

namespace Smartmage\Inpost\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Smartmage\Inpost\Model\Config\Source\ShippingMethods;

class Service implements OptionSourceInterface
{

    public function __construct(
        ShippingMethods $shippingMethods
    ) {
        $this->shippingMethods = $shippingMethods;
    }

    const SERVICE_LABEL = [
        'inpost_locker_standard' => 'Standard parcel locker delivery',
        'inpost_locker_customer_service_point' => 'Standard parcel locker delivery',
        'inpost_courier_standard' => 'Standard courier shipment',
        'inpost_courier_express_1000' => 'Courier shipment with delivery until 10:00 on the next day',
        'inpost_courier_express_1200' => 'Courier shipment with delivery until 12:00 on the next day',
        'inpost_courier_express_1700' => 'Courier shipment with delivery until 17:00 on the next day',
        'inpost_courier_palette' => 'Paleta Standard courier shipment',
        'inpost_courier_c2c' => 'Standard courier shipment c2c'
    ];

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
