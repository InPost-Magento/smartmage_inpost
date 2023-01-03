<?php

declare(strict_types=1);

namespace Smartmage\Inpost\Model\Carrier\Methods\Courier;

use Smartmage\Inpost\Model\Carrier\Methods\AbstractMethod;
use Smartmage\Inpost\Model\Config\Source\ShippingMethodsMode;

class Palette extends AbstractMethod
{
    public string $methodKey = 'palette';

    public string $carrierCode = 'inpostcourier';

    protected string $blockAttribute = 'block_send_with_palette';

    public int $shippingMethodsMode = ShippingMethodsMode::SHIPPING_METHODS_MODE_STANDARD;
}
