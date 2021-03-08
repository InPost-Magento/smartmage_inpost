<?php

declare(strict_types=1);

namespace Smartmage\Inpost\Model\Carrier\Methods\Locker;

use Smartmage\Inpost\Model\Carrier\AbstractMethod;

class StandardCod extends AbstractMethod
{
    public $methodKey = 'standardcod';

    public $carrierCode = 'inpostlocker';

    protected $blockAttribute = 'block_send_with_locker';
}
