<?php

declare(strict_types=1);

namespace Smartmage\Inpost\Model\Carrier\Methods\Locker;

use Smartmage\Inpost\Model\Carrier\Methods\AbstractMethod;

class Standard extends AbstractMethod
{
    public string $methodKey = 'standard';

    public string $carrierCode = 'inpostlocker';

    /**
     * @var string
     */
    protected $blockAttribute = 'block_send_with_locker';

    /**
     * @var int
     */
    protected int $shippingMethodsMode = 0;
}
