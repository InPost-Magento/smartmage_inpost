<?php
declare(strict_types=1);

namespace Smartmage\Inpost\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Mode implements OptionSourceInterface
{
    const TEST = 'test';
    const PROD = 'prod';

    const TEST_BASE_URI = 'https://sandbox-api-shipx-pl.easypack24.net';
    const PROD_BASE_URI = 'https://api-shipx-pl.easypack24.net';

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
