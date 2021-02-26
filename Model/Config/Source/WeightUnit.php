<?php
declare(strict_types=1);

namespace Smartmage\Inpost\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class WeightUnit
 */
class WeightUnit implements OptionSourceInterface
{
    const KILOGRAM = 'kg';
    const GRAM = 'g';

    /**
     * {@inheritdoc}
     */
    public function toOptionArray() : array
    {
        return [
            ['value' => self::KILOGRAM, 'label' => __('Kilograms')],
            ['value' => self::GRAM, 'label' => __('Grams')],
        ];
    }
}
