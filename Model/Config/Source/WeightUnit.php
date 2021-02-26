<?php

namespace Smartmage\Inpost\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Type
 */
class WeightUnit implements OptionSourceInterface
{
    const KILOGRAM = 'kg';
    const GRAM = 'g';

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::KILOGRAM, 'label' => __('Kilograms')],
            ['value' => self::GRAM, 'label' => __('Grams')],
        ];
    }
}
