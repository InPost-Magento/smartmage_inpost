<?php
declare(strict_types=1);

namespace Smartmage\Inpost\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class DefaultWaySending
 */
class DefaultWaySending implements OptionSourceInterface
{
    const INPOST_LOCKER_STANDARD = 'standard';
    const INPOST_COURIER_C2C = 'c2c';
    const INPOST_COURIER_STANDARD = 'courier_standard';
    const INPOST_COURIER_EXPRESS1000 = 'express1000';
    const INPOST_COURIER_EXPRESS1200 = 'express1200';
    const INPOST_COURIER_EXPRESS1700 = 'express1700';
    const INPOST_COURIER_LOCAL_STANDARD = 'local_standard';
    const INPOST_COURIER_LOCAL_EXPRESS = 'local_express';
    const INPOST_COURIER_LOCAL_SUPER_EXPRESS = 'local_super_express';

    protected $code = '';

    /**
     * DefaultWaySending constructor.
     * @param null $code
     */
    public function __construct($code = null)
    {
        $this->code = $code;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray() : array
    {
        switch ($this->code) {
            case (self::INPOST_LOCKER_STANDARD):
                return [
                    ['value' => 'parcel_locker', 'label' => __('Nadanie w Paczkomacie')],
                    ['value' => 'dispatch_order', 'label' => __('Odbiór przez Kuriera')],
                    ['value' => 'pop', 'label' => __('Nadanie w POP')],
                ];
            case (self::INPOST_COURIER_C2C):
                return [
                    ['value' => 'dispatch_order', 'label' => __('Odbiór przez kuriera')],
                    ['value' => 'pop', 'label' => __('Nadanie w POP')],
                    ['value' => 'parcel_locker', 'label' => __('Nadanie w paczkomacie')]
                ];
            case (self::INPOST_COURIER_STANDARD):
            case (self::INPOST_COURIER_EXPRESS1000):
            case (self::INPOST_COURIER_EXPRESS1200):
            case (self::INPOST_COURIER_EXPRESS1700):
            case (self::INPOST_COURIER_LOCAL_STANDARD):
            case (self::INPOST_COURIER_LOCAL_EXPRESS):
            case (self::INPOST_COURIER_LOCAL_SUPER_EXPRESS):
                return [
                    ['value' => 'dispatch_order', 'label' => __('Odbiór przez Kuriera')],
                    ['value' => 'pop', 'label' => __('Nadanie w POP')],

                ];
            default:
                return [];
        }
    }

    public function setCode($code)
    {
        $this->code = $code;
    }
}
