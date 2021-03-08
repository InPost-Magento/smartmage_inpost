<?php

declare(strict_types=1);

namespace Smartmage\Inpost\Model\Carrier;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Smartmage\Inpost\Model\ConfigProvider;

class AbstractMethod
{
    /**
     * @var string
     */
    public $methodKey;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var String
     */
    public $carrierCode;

    /**
     * @var ConfigProvider
     */
    protected $configProvider;

    /**
     * @var mixed
     */
    protected $quote;

    protected $quoteItems;

    /**
     * @var string
     */
    protected $blockAttribute;

    /**
     * AbstractMethod constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param String $carrierCode
     * @param ConfigProvider $configProvider
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        String $carrierCode,
        ConfigProvider $configProvider,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->carrierCode = $carrierCode;
        $this->configProvider = $configProvider;
        $this->storeManager = $storeManager;
        $this->quote = $this->getQuote();
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isAllowed()
    {
        //Darmowa dostawa od - done
        //Maksymalna waga koszyka done
        //Dopuszczalność wysyłki danego koszyka
        //Okienko czasowe metody dostawy w weekend

        //Check if method is active
        if (!$this->configProvider->getConfigFlag($this->carrierCode . '/' . $this->methodKey . '/active')) {
            return false;
        }

        //Check if products have disabled shipping method type
        if ($this->isShippingDisabled()) {
            return false;
        }

        //Checking that the products do not weigh too much
        $maxWeight = $this->configProvider->getConfigData(
            $this->carrierCode . '/' . $this->methodKey .'/max_cart_weight'
        );
        if ($this->calculateWeight() > $maxWeight) {
            return false;
        }
    }

    protected function isWeekendSendAvailable(): bool
    {
        return false;
    }

    protected function calculateWeight()
    {
        $customWeightAttribute = $this->configProvider->getConfigData(
            'weight_attribute_code'
        );
        $weight = 0;

        if ($customWeightAttribute) {
            $storeId = $this->storeManager->getStore()->getId();
            foreach ($this->quoteItems as $item) {
                $quoteProduct = $item->getProduct();
                $weight += $quoteProduct->getResource()->getAttributeRawValue(
                    $quoteProduct->getId(),
                    $customWeightAttribute,
                    $storeId
                );
            }
        } else {
            foreach ($this->quoteItems as $item) {
                $weight += ($item->getWeight() * $item->getQty());
            }
        }

        return $weight;
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function isShippingDisabled()
    {
        $storeId = $this->storeManager->getStore()->getId();
        foreach ($this->quoteItems as $item) {
            $product = $item->getProduct();
            $blockShip = $product->getResource()->getAttributeRawValue(
                $product->getId(),
                $this->blockAttribute,
                $storeId
            );

            if ($blockShip) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return int
     */
    public function calculatePrice()
    {
        if ($this->isFreeShipping()) {
            return 0;
        }

        return $this->configProvider->getConfigData(
            $this->carrierCode . '/' . $this->methodKey . '/price'
        );
    }

    /**
     * @return mixed
     */
    public function isFreeShipping()
    {
        if ($this->configProvider->getConfigFlag(
            $this->carrierCode . '/' . $this->methodKey . '/free_shipping_enable'
        )) {
            $freeShippingFrom = $this->configProvider->getConfigData(
                $this->carrierCode . '/' . $this->methodKey . '/free_shipping_subtotal'
            );

            $total = $this->getQuoteTotal();

            if ($total >= $freeShippingFrom) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->methodKey;
    }

    protected function getQuoteTotal($quote)
    {
        $total = 0;

        foreach ($this->quoteItems as $item) {
            $total += $item->getQty()*$item->getPrice();
        }

        return $total;
    }

    public function setItems($quoteItems)
    {
        $this->quoteItems = $quoteItems;
    }
}
