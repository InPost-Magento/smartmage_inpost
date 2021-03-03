<?php

declare(strict_types=1);

namespace Smartmage\Inpost\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrier;

/**
 * Class InpostLocker for locker carrier
 */
class InpostLocker extends AbstractCarrier
{

    public function collectRates(RateRequest $request)
    {
        // TODO: Implement collectRates() method.
    }

    private function getMethods()
    {
        return [];
    }
}
