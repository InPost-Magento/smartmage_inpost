<?php

declare(strict_types=1);

namespace Smartmage\Inpost\Model\Carrier\Methods\Courier;

use Smartmage\Inpost\Model\Carrier\AbstractMethod;

class C2cCod extends AbstractMethod
{
    protected $methodKey = 'c2ccod';

    protected $carrierCode = 'inpostcourier';
}
