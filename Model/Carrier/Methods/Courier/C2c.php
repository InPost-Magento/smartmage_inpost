<?php

declare(strict_types=1);

namespace Smartmage\Inpost\Model\Carrier\Methods\Courier;

use Smartmage\Inpost\Model\Carrier\AbstractMethod;

class C2c extends AbstractMethod
{
    protected $methodKey = 'c2c';

    protected $carrierCode = 'inpostcourier';
}
