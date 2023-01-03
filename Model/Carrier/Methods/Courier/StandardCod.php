<?php

declare(strict_types=1);

namespace Smartmage\Inpost\Model\Carrier\Methods\Courier;

use Smartmage\Inpost\Model\Carrier\Methods\AbstractMethod;
use Smartmage\Inpost\Model\Config\Source\ShippingMethodsMode;

class StandardCod extends AbstractMethod
{
    public string $methodKey = 'standardcod';

    public string $carrierCode = 'inpostcourier';

    protected string $blockAttribute = 'block_send_with_courier';

    public int $shippingMethodsMode = ShippingMethodsMode::SHIPPING_METHODS_MODE_STANDARD;
}
