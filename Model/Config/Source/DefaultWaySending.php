<?php
declare(strict_types=1);

namespace Smartmage\Inpost\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class DefaultWaySending implements OptionSourceInterface
{
    const INPOST_LOCKER_STANDARD = 'inpostlocker_standard';
    const INPOST_LOCKER_STANDARD_COD = 'inpostlocker_standardcod';
    const INPOST_LOCKER_STANDARD_EOW = 'inpostlocker_standardeow';
    const INPOST_LOCKER_STANDARD_EOW_COD = 'inpostlocker_standardeowcod';
    const INPOST_COURIER_C2C = 'inpostcourier_c2c';
    const INPOST_COURIER_C2C_COD = 'inpostcourier_c2ccod';
    const INPOST_COURIER_STANDARD = 'inpostcourier_standard';
    const INPOST_COURIER_STANDARD_COD = 'inpostcourier_standardcod';
    const INPOST_COURIER_EXPRESS1000 = 'inpostcourier_express1000';
    const INPOST_COURIER_EXPRESS1200 = 'inpostcourier_express1200';
    const INPOST_COURIER_EXPRESS1700 = 'inpostcourier_express1700';
    const INPOST_COURIER_LOCAL_STANDARD = 'inpostcourier_localstandard';
    const INPOST_COURIER_LOCAL_EXPRESS = 'inpostcourier_localexpress';
    const INPOST_COURIER_LOCAL_SUPER_EXPRESS = 'inpostcourier_localsuperexpress';
    const INPOST_COURIER_PALETTE = 'inpostcourier_palette';

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
            case (self::INPOST_LOCKER_STANDARD_COD):
                return [
                    ['value' => 'parcel_locker', 'label' => __('Nadanie w Paczkomacie')],
                    ['value' => 'dispatch_order', 'label' => __('Odbiór przez Kuriera')],
                    ['value' => 'pop', 'label' => __('Nadanie w POP')],
                ];
            case (self::INPOST_LOCKER_STANDARD_EOW):
            case (self::INPOST_LOCKER_STANDARD_EOW_COD):
                return [
                    ['value' => 'parcel_locker', 'label' => __('Nadanie w Paczkomacie')],
                    ['value' => 'dispatch_order', 'label' => __('Odbiór przez Kuriera')],
                ];
            case (self::INPOST_COURIER_C2C):
            case (self::INPOST_COURIER_C2C_COD):
                return [
                    ['value' => 'dispatch_order', 'label' => __('Odbiór przez kuriera')],
                    ['value' => 'pop', 'label' => __('Nadanie w POP')],
                    ['value' => 'parcel_locker', 'label' => __('Nadanie w paczkomacie')]
                ];
            case (self::INPOST_COURIER_STANDARD):
            case (self::INPOST_COURIER_STANDARD_COD):
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
            case (self::INPOST_COURIER_PALETTE):
                return [
                    ['value' => 'dispatch_order', 'label' => __('Odbiór przez Kuriera')]
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
