<?php
declare(strict_types=1);

namespace Smartmage\Inpost\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class SendingMethod implements OptionSourceInterface
{
    const PARCEL_LOCKER = 'parcel_locker';
    const POK = 'pok';
    const COURIER_POK = 'courier_pok';
    const BRANCH = 'branch';
    const DISPATCH_ORDER = 'dispatch_order';
    const POP = 'pop';

    const SENDING_METHOD_LABEL = [
        'parcel_locker' => 'sent in an automatic parcel station',
        'pok' => 'sent in the Customer Service Point',
        'courier_pok' => 'sent in the Customer Service Point handling shipping of courier shipments',
        'branch' => 'sent in a Branch',
        'dispatch_order' => 'Collection order - courier order',
        'pop' => 'sent in the Shipment Service Point',
    ];

    /**
     * {@inheritdoc}
     */
    public function toOptionArray() : array
    {
        $sendingMethods = [];

        foreach (self::SENDING_METHOD_LABEL as $key => $value) {
            $sendingMethods[] = ['value' => $key, 'label' => __($value)];
        }
        return $sendingMethods;
    }

    /**
     * @param $sendingMethod
     * @return \Magento\Framework\Phrase
     */
    public function getSendingMethodLabel($sendingMethod)
    {
        return isset(self::SENDING_METHOD_LABEL[$sendingMethod]) ?
            __(self::SENDING_METHOD_LABEL[$sendingMethod]) : $sendingMethod;
    }
}
