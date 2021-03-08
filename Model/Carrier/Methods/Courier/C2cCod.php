<?php

declare(strict_types=1);

namespace Smartmage\Inpost\Model\Carrier\Methods\Courier;

use Smartmage\Inpost\Model\Carrier\AbstractMethod;

class C2cCod extends AbstractMethod
{
    public $methodKey = 'c2ccod';

    public $carrierCode = 'inpostcourier';

    protected $blockAttribute = 'block_send_with_courier';
}
