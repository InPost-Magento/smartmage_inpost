<?php
declare(strict_types=1);

namespace Smartmage\Inpost\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Size implements OptionSourceInterface
{
    const SMALL = 'small';
    const MEDIUM = 'medium';
    const LARGE = 'large';
    const XLARGE = 'xlarge';

    const SIZE_LABEL = [
        'small' => 'Size A',
        'medium' => 'Size B',
        'large' => 'Size C',
        'xlarge' => 'Size D',
    ];

    protected $c2cMethods = ['inpost_courier_c2c', 'inpost_courier_c2ccod'];

    protected $shippingMethod;

    /**
     * {@inheritdoc}
     */
    public function toOptionArray() : array
    {
        $sizes = [
            ['value' => self::SMALL, 'label' => __(self::SIZE_LABEL[self::SMALL])],
            ['value' => self::MEDIUM, 'label' => __(self::SIZE_LABEL[self::MEDIUM])],
            ['value' => self::LARGE, 'label' => __(self::SIZE_LABEL[self::LARGE])]
        ];

        if (in_array($this->shippingMethod, $this->c2cMethods) || !$this->shippingMethod) {
            $sizes[] = ['value' => self::XLARGE, 'label' => __(self::SIZE_LABEL[self::XLARGE])];
        }

        return $sizes;
    }

    public function setShippingMethod($shippingMethod)
    {
        $this->shippingMethod = $shippingMethod;
    }

    public function getSizeLabel($size)
    {
        return isset(self::SIZE_LABEL[$size]) ? __(self::SIZE_LABEL[$size]) : $size;
    }
}
