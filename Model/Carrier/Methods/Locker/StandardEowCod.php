<?php

declare(strict_types=1);

namespace Smartmage\Inpost\Model\Carrier\Methods\Locker;

use Smartmage\Inpost\Model\Carrier\AbstractMethod;

class StandardEowCod extends AbstractMethod
{
    protected $methodKey = 'standardeowcod';

    protected $carrierCode = 'inpostlocker';
}
