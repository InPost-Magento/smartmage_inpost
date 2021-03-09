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
    const INPOST_LOCKER_STANDARD_COD = 'standard_cod';

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
                    ['value' => 'branch', 'label' => __('Nadanie w Oddziale')],
                    ['value' => 'dispatch_order', 'label' => __('Odbiór przez Kuriera')],
                    ['value' => 'pop', 'label' => __('Nadanie w POP')],
                ];
            case (self::INPOST_LOCKER_STANDARD_COD):
                return [
                    ['value' => 'UPS', 'label' => __('United Parcel Service')],
                    ['value' => 'UPS_XML', 'label' => __('United Parcel Service XML')]
                ];
            default:
                return [];
        }
    }
}
