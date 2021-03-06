<?php

declare(strict_types=1);

namespace Smartmage\Inpost\Model\Carrier\Methods\Courier;

use Smartmage\Inpost\Model\Carrier\AbstractMethod;

class Express1000 extends AbstractMethod
{
    protected $methodKey = 'express1000';

    protected $carrierCode = 'inpostcourier';
}
