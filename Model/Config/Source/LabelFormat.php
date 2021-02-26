<?php

namespace Smartmage\Inpost\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class LabelFormat
 */
class LabelFormat implements OptionSourceInterface
{
    const PDF = 'pdf';
    const EPL = 'epl';
    const ZPL = 'zpl';

    /**
     * {@inheritdoc}
     */
    public function toOptionArray() : array
    {
        return [
            ['value' => self::PDF, 'label' => __('PDF')],
            ['value' => self::EPL, 'label' => __('EPL')],
            ['value' => self::ZPL, 'label' => __('ZPL')],
        ];
    }
}
