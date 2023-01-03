<?php

namespace Smartmage\Inpost\Model\Carrier;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Rate\Result;
use Smartmage\Inpost\Model\ConfigProvider;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Psr\Log\LoggerInterface;
use Magento\Checkout\Model\Session;

abstract class AbstractInpostCarrier extends AbstractCarrier
{

    /**
     * @var ResultFactory
     */
    protected $rateResultFactory;

    /**
     * @var MethodFactory
     */
    protected $rateMethodFactory;

    /**
     * @var array
     */
    protected $methods;

    /**
     * @var ConfigProvider
     */
    protected $configProvider;

    /**
     * @var Session
     */
    protected $checkoutSession;

    protected $allowedMethods;

    protected $eowMethods = [
        'standardeow',
        'standardeowcod'
    ];

    protected $eowAvailable = false;

    /**
     * AbstractInpostCarrier constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param array $methods
     * @param ConfigProvider $configProvider
     * @param Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        array $methods,
        ConfigProvider $configProvider,
        Session $checkoutSession,
        array $data = []
    ) {
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->methods = $methods;
        $this->configProvider = $configProvider;
        $this->checkoutSession = $checkoutSession;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * @param RateRequest $request
     * @return Result
     */
    public function collectRates(RateRequest $request)
    {
        /** @var Result $result */
        $result = $this->rateResultFactory->create();

        $this->getActiveAllowedMethods($request);

        foreach ($this->allowedMethods as $method) {
            $result->append(
                $this->createResultMethod($method)
            );
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getActiveAllowedMethods(RateRequest $request): array
    {
        $allowedMethods = [];
        $methods = [];
        $quoteItems = $request->getAllItems();

        foreach ($this->methods as $method) {

            \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class)->debug($method->getKey() . 'xx');
            $method->setItems($quoteItems);
            if ($method->isAllowed()) {
                \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class)->debug('allowed ok');
            }
            if ($method->isAllowed()
                && $this->isShipCountryApplicable($request->getDestCountryId(), $method->getKey())
            ) {
                \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class)->debug('jest ok');
                if (in_array($method->getKey(), $this->eowMethods)) {
                    $this->eowAvailable = true;
                }
                $allowedMethods[] = [
                    'key' => $method->getKey(),
                    'sort' => $this->configProvider->getConfigData(
                        $this->_code . '/' . $method->getKey() . '/position'
                    ),
                    'price' => $method->calculatePrice($request)
                ];

                $methods[$this->_code] = $method->getName();
            }
        }

        $sort = array_column($allowedMethods, "sort");
        array_multisort($sort, SORT_ASC, $allowedMethods);

        $this->allowedMethods = $allowedMethods;

        return $methods;
    }

    /**
     * @return boolean
     */
    public function isShipCountryApplicable($destCountryId, $methodKey)
    {
        $speCountriesAllow = $this->configProvider->getConfigData($this->_code . '/' . $methodKey . '/sallowspecific');
        /*
         * for specific countries, the flag will be 1
         */
        if ($speCountriesAllow && $speCountriesAllow == 1) {
            $availableCountries = [];
            if ($this->configProvider->getConfigData($this->_code . '/' . $methodKey . '/specificcountry')) {
                $availableCountries = explode(',', $this->configProvider->getConfigData($this->_code . '/' . $methodKey . '/specificcountry'));
            }
            if ($availableCountries && in_array($destCountryId, $availableCountries)) {
                return true;
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array
     */
    public function getAllowedMethods($forShippingMethodsMode = false) : array
    {
        $methods = [];
        foreach ($this->methods as $method) {
            if($forShippingMethodsMode === true
                && $method->shippingMethodsMode != $this->configProvider->getConfigData('inpostlocker/shippingmethodsmode')) {
                continue;
            }
            $methods[$method->getKey()] = $method->getName();
        }
        return $methods;
    }

    /**
     * @param $method
     * @return \Magento\Quote\Model\Quote\Address\RateResult\Method
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function createResultMethod($method)
    {
        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $rateMethod */
        $rateMethod = $this->rateMethodFactory->create();

        $rateMethod->setCarrier($this->_code);
        $rateMethod->setCarrierTitle($this->configProvider->getConfigData($this->_code . '/title'));

        $rateMethod->setMethod($method['key']);
        $rateMethod->setMethodTitle($this->configProvider->getConfigData($this->_code.'/'.$method['key'] . '/name'));

        $rateMethod->setPrice($method['price']);
        $rateMethod->setCost($method['price']);
        return $rateMethod;
    }

    /**
     * @return mixed
     */
    protected function getQuoteItems()
    {
        return $this->checkoutSession->getQuote()->getItems();
    }

    /**
     * @return bool
     */
    public function isTrackingAvailable(): bool
    {
        return true;
    }
}
