<?php

declare(strict_types=1);

namespace Smartmage\Inpost\Model\Carrier\Methods\Courier;

use Smartmage\Inpost\Model\Carrier\AbstractMethod;

class LocalSuperExpress extends AbstractMethod
{
    protected $methodKey = 'localsuperexpress';

    protected $carrierCode = 'inpostcourier';
}
