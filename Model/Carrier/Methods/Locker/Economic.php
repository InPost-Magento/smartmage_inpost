<?php

declare(strict_types=1);

namespace Smartmage\Inpost\Model\Carrier\Methods\Locker;

use Smartmage\Inpost\Model\Carrier\Methods\AbstractMethod;
use Smartmage\Inpost\Model\Config\Source\ShippingMethodsMode;

class Economic extends AbstractMethod
{
    public string $methodKey = 'economic';

    public string $carrierCode = 'inpostlocker';

    protected string $blockAttribute = 'block_send_with_locker';

    protected int $shippingMethodsMode = ShippingMethodsMode::SHIPPING_METHODS_MODE_ECONOMIC;
}
