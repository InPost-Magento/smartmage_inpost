<?php

declare(strict_types=1);

namespace Smartmage\Inpost\Model\Carrier\Methods\Courier;

use Smartmage\Inpost\Model\Carrier\AbstractMethod;

class Standard extends AbstractMethod
{
    protected $methodKey = 'standard';

    protected $carrierCode = 'inpostcourier';
}
