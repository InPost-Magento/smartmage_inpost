<?php
declare(strict_types=1);

namespace Smartmage\Inpost\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Size implements OptionSourceInterface
{
    const USE_PRODUCT_ATTRIBUTE = 'product_attribute';
    const SMALL = 'small';
    const MEDIUM = 'medium';
    const LARGE = 'large';
    const XLARGE = 'xlarge';

    const SIZE_LABEL = [
        'product_attribute' => 'Use product attribute',
        'small' => 'Size A',
        'medium' => 'Size B',
        'large' => 'Size C',
        'xlarge' => 'Size D',
    ];

    protected $c2cMethods = ['inpost_courier_c2c', 'inpost_courier_c2ccod'];

    protected $shippingMethod;
    protected bool $includeProductAttribute = true;

    /**
     * {@inheritdoc}
     */
    public function toOptionArray() : array
    {
        $sizes = [];
        if($this->includeProductAttribute) {
            $sizes[] = ['value' => self::USE_PRODUCT_ATTRIBUTE, 'label' => __(self::SIZE_LABEL[self::USE_PRODUCT_ATTRIBUTE])];
        };
        array_push($sizes,
            ['value' => self::SMALL, 'label' => __(self::SIZE_LABEL[self::SMALL])],
            ['value' => self::MEDIUM, 'label' => __(self::SIZE_LABEL[self::MEDIUM])],
            ['value' => self::LARGE, 'label' => __(self::SIZE_LABEL[self::LARGE])]
        );

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

    public function setIncludeProductAttribute(bool $includeProductAttribute): void
    {
        $this->includeProductAttribute = $includeProductAttribute;
    }
}
