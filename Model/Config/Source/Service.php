<?php
declare(strict_types=1);

namespace Smartmage\Inpost\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Service implements OptionSourceInterface
{

    const SERVICE_LABEL = [
        'inpost_locker_standard' => 'Standard parcel locker delivery',
        'inpost_locker_customer_service_point' => 'Standard parcel locker delivery',
        'inpost_courier_standard' => 'Standard courier shipment',
        'inpost_courier_express_1000' => 'Courier shipment with delivery until 10:00 on the next day',
        'inpost_courier_express_1200' => 'Courier shipment with delivery until 12:00 on the next day',
        'inpost_courier_express_1700' => 'Courier shipment with delivery until 17:00 on the next day',
        'inpost_courier_local_standard' => 'Local Standard courier shipment',
        'inpost_courier_local_express' => 'Local Express courier shipment',
        'inpost_courier_local_super_express' => 'Super Express courier shipment',
        'inpost_courier_palette' => 'Paleta Standard courier shipment',
        'inpost_courier_c2c' => 'Standard courier shipment c2c'
    ];

    /**
     * {@inheritdoc}
     */
    public function toOptionArray() : array
    {
        $services = [];

        foreach (self::SERVICE_LABEL as $key => $value) {
            $services[] = ['value' => $key, 'label' => __($value)];
        }

        return $services;
    }

    /**
     * @param $service
     * @return \Magento\Framework\Phrase
     */
    public function getServiceLabel($service)
    {
        return (isset(self::SERVICE_LABEL[$service])) ? __(self::SERVICE_LABEL[$service]) : $service;
    }
}
