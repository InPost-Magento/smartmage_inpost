<?php
declare(strict_types=1);

namespace Smartmage\Inpost\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class ShippingMethodsMode implements OptionSourceInterface
{
    const SHIPPING_METHODS_MODE_STANDARD = 0;
    const SHIPPING_METHODS_MODE_ECONOMIC = 1;

    /**
     * {@inheritdoc}
     */
    public function toOptionArray() : array
    {
        return [
            ['value' => self::SHIPPING_METHODS_MODE_STANDARD, 'label' => __('Standard mode')],
            ['value' => self::SHIPPING_METHODS_MODE_ECONOMIC, 'label' => __('Economic mode')]
        ];
    }
}
