<?php

declare(strict_types=1);

namespace Smartmage\Inpost\Model\Carrier\Methods\Locker;

use Smartmage\Inpost\Model\Carrier\Methods\AbstractMethod;

class EconomicCod extends AbstractMethod
{
    public string $methodKey = 'economiccod';

    public string $carrierCode = 'inpostlocker';

    protected string $blockAttribute = 'block_send_with_locker';
}
