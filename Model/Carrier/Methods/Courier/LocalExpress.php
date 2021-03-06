<?php

declare(strict_types=1);

namespace Smartmage\Inpost\Model\Carrier\Methods\Courier;

use Smartmage\Inpost\Model\Carrier\AbstractMethod;

class LocalExpress extends AbstractMethod
{
    protected $methodKey = 'localexpress';

    protected $carrierCode = 'inpostcourier';
}
