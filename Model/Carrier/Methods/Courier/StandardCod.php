<?php

declare(strict_types=1);

namespace Smartmage\Inpost\Model\Carrier\Methods\Courier;

use Smartmage\Inpost\Model\Carrier\Methods\AbstractMethod;

class StandardCod extends AbstractMethod
{
    public string $methodKey = 'standardcod';

    public string $carrierCode = 'inpostcourier';

    protected string $blockAttribute = 'block_send_with_courier';
}
