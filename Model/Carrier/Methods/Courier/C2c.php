<?php

declare(strict_types=1);

namespace Smartmage\Inpost\Model\Carrier\Methods\Courier;

use Smartmage\Inpost\Model\Carrier\Methods\AbstractMethod;

class C2c extends AbstractMethod
{
    public $methodKey = 'c2c';

    public $carrierCode = 'inpostcourier';

    protected $blockAttribute = 'block_send_with_courier';
}
