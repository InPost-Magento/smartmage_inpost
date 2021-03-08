<?php

declare(strict_types=1);

namespace Smartmage\Inpost\Model\Carrier\Methods\Locker;

use Smartmage\Inpost\Model\Carrier\AbstractMethod;

class Standard extends AbstractMethod
{
    public $methodKey = 'standard';

    public $carrierCode = 'inpostlocker';

    /**
     * @var string
     */
    protected $blockAttribute = 'block_send_with_locker';
}
