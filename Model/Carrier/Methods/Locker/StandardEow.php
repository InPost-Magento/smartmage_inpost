<?php

declare(strict_types=1);

namespace Smartmage\Inpost\Model\Carrier\Methods\Locker;

use Smartmage\Inpost\Model\Carrier\AbstractMethod;

class StandardEow extends AbstractMethod
{
    protected $methodKey = 'standardeow';

    protected $carrierCode = 'inpostlocker';
}
