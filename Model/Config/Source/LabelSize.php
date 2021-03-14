<?php
declare(strict_types=1);

namespace Smartmage\Inpost\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class LabelSize
 */
class LabelSize implements OptionSourceInterface
{
    const A4 = 'a4';
    const A6 = 'a6';

    const PDF = 'pdf';
    const EPL = 'epl';
    const ZPL = 'zpl';

    protected $code = '';

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
            case (self::PDF):
                return [
                    ['value' => self::A4, 'label' => __('A4')],
                ];
            case (self::EPL):
            case (self::ZPL):
                return [
                    ['value' => self::A4, 'label' => __('A4')],
                    ['value' => self::A6, 'label' => __('A6')],
                ];
            default:
                return [];
        }
    }
}
