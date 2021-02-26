<?php

namespace Smartmage\Inpost\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class DayOfTheWeek
 */
class DayOfTheWeek implements OptionSourceInterface
{
    const MONDAY = 1;
    const TUESDAY = 2;
    const WEDNESDAY = 3;
    const THURSDAY = 4;
    const FRIDAY = 5;
    const SATURDAY = 6;
    const SUNDAY = 7;

    /**
     * {@inheritdoc}
     */
    public function toOptionArray() : array
    {
        return [
            ['value' => self::MONDAY, 'label' => __('Monday')],
            ['value' => self::TUESDAY, 'label' => __('Tuesday')],
            ['value' => self::WEDNESDAY, 'label' => __('Wednesday')],
            ['value' => self::THURSDAY, 'label' => __('Thursday')],
            ['value' => self::FRIDAY, 'label' => __('Friday')],
            ['value' => self::SATURDAY, 'label' => __('Saturday')],
            ['value' => self::SUNDAY, 'label' => __('Sunday')],
        ];
    }
}
