<?php

declare(strict_types=1);

namespace Smartmage\Inpost\Model\Carrier;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Smartmage\Inpost\Model\ConfigProvider;
use Magento\Checkout\Model\SessionFactory as CheckoutSessionFactory;

class AbstractMethod
{
    /**
     * @var string
     */
    protected $methodKey;

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
    protected $carrierCode;

    /**
     * @var ConfigProvider
     */
    protected $configProvider;

    /**
     * @var CheckoutSessionFactory
     */
    protected $checkoutSessionFactory;

    /**
     * AbstractMethod constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param String $carrierCode
     * @param ConfigProvider $configProvider
     * @param CheckoutSessionFactory $checkoutSessionFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        String $carrierCode,
        ConfigProvider $configProvider,
        CheckoutSessionFactory $checkoutSessionFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->carrierCode = $carrierCode;
        $this->configProvider = $configProvider;
        $this->checkoutSessionFactory = $checkoutSessionFactory;
    }

    public function isAllowed()
    {
        //Darmowa dostawa od
        //Maksymalna waga koszyka
        //Dopuszczalność wysyłki danego koszyka
        //Okienko czasowe metody dostawy w weekend

        return true;
    }

    /**
     * @return int
     */
    public function calculatePrice()
    {
        if ($this->isFreeShipping()) {
            return 0;
        }
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

    /**
     * @return mixed
     */
    private function getQuote()
    {
        return $this->checkoutSessionFactory->create->getQuote();
    }
}
