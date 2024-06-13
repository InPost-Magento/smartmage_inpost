<?php

declare(strict_types=1);

namespace Smartmage\Inpost\Model\Carrier\Methods\Courier;

use Smartmage\Inpost\Model\Carrier\Methods\AbstractMethod;
use Smartmage\Inpost\Setup\Patch\Data\AddProductAlcoholAttribute;

class Alcohol extends AbstractMethod
{
    public string $methodKey = 'alcohol';

    public string $carrierCode = 'inpostcourier';

    protected string $blockAttribute = 'block_send_with_locker';

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function isShippingDisabled()
    {
        if(parent::isShippingDisabled()) {
            return true;
        }
        $storeId = $this->storeManager->getStore()->getId();
        foreach ($this->quoteItems as $item) {
            $product = $item->getProduct();
            $allowAlcohol = $product->getResource()->getAttributeRawValue(
                $product->getId(),
                AddProductAlcoholAttribute::INPOST_ALCOHOL,
                $storeId
            );

            if (false === $allowAlcohol) {
                return true;
            }
        }

        return false;
    }
}
