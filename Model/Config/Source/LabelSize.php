<?php
declare(strict_types=1);

namespace Smartmage\Inpost\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class LabelSize implements OptionSourceInterface
{
    const normal = 'normal'; //A4
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
        return [
            ['value' => self::normal, 'label' => __('A4')],
            ['value' => self::A6, 'label' => __('A6')]
        ];
    }
}
