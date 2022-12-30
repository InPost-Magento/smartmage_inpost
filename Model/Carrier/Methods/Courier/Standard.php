<?php

declare(strict_types=1);

namespace Smartmage\Inpost\Model\Carrier\Methods\Courier;

use Smartmage\Inpost\Model\Carrier\Methods\AbstractMethod;

class Standard extends AbstractMethod
{
    public string $methodKey = 'standard';

    public string $carrierCode = 'inpostcourier';

    protected $blockAttribute = 'block_send_with_courier';

    /**
     * @var int
     */
    protected int $shippingMethodsMode = 0;
}
