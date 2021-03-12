<?php
declare(strict_types=1);

namespace Smartmage\Inpost\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Mode
 */
class Size implements OptionSourceInterface
{
    const SMALL = 'small';
    const MEDIUM = 'medium';
    const LARGE = 'large';
    const XLARGE = 'xlarge';

    protected $c2cMethods = ['inpost_courier_c2c', 'inpost_courier_c2ccod'];

    protected $shippingMethod;

    /**
     * {@inheritdoc}
     */
    public function toOptionArray() : array
    {
        $sizes = [
            ['value' => self::SMALL, 'label' => __('Small')],
            ['value' => self::MEDIUM, 'label' => __('Medium')],
            ['value' => self::LARGE, 'label' => __('Large')]
        ];

        if (in_array($this->shippingMethod, $this->c2cMethods) || !$this->shippingMethod) {
            $sizes[] = ['value' => self::XLARGE, 'label' => __('X Large')];
        }

        return $sizes;
    }

    public function setShippingMethod($shippingMethod)
    {
        $this->shippingMethod = $shippingMethod;
    }
}
