<?php
declare(strict_types=1);

namespace Smartmage\Inpost\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class LabelFormat implements OptionSourceInterface
{
    const PDF = 'pdf';
    const EPL = 'epl';
    const ZPL = 'zpl';
    const ZIP = 'zip';

    const STRING_SIZE = 'size';
    const STRING_FORMAT = 'format';

    const LABEL_CONTENT_TYPES = [
        self::PDF => 'application/pdf',
        self::EPL => 'application/epl2',
        self::ZPL => 'application/zpl',
        self::ZIP => 'application/zip',
    ];

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
