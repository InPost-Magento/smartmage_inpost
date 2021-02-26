<?php

namespace Smartmage\Inpost\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Mode
 */
class Mode implements OptionSourceInterface
{
    const TEST = 'test';
    const PROD = 'prod';

    /**
     * {@inheritdoc}
     */
    public function toOptionArray() : array
    {
        return [
            ['value' => self::TEST, 'label' => __('Test')],
            ['value' => self::PROD, 'label' => __('Production')],
        ];
    }
}
