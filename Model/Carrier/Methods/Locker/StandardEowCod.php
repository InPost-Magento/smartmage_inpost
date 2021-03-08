<?php

declare(strict_types=1);

namespace Smartmage\Inpost\Model\Carrier\Methods\Locker;

use Smartmage\Inpost\Model\Carrier\AbstractMethod;

class StandardEowCod extends AbstractMethod
{
    public $methodKey = 'standardeowcod';

    public $carrierCode = 'inpostlocker';

    protected $blockAttribute = 'block_send_with_locker';
}
