<?php

namespace Smartmage\Inpost\Model\Carrier;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Rate\Result;
use Smartmage\Inpost\Model\ConfigProvider;
use Magento\Checkout\Model\SessionFactory as CheckoutSessionFactory;

class AbstractInpostCarrier extends AbstractCarrier
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
     * @var CheckoutSessionFactory
     */
    protected $checkoutSessionFactory;

    /**
     * AbstractInpostCarrier constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param array $methods
     * @param ConfigProvider $configProvider
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
        CheckoutSessionFactory $checkoutSessionFactory,
        array $data = []
    ) {
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->methods = $methods;
        $this->configProvider = $configProvider;
        $this->checkoutSessionFactory = $checkoutSessionFactory;
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

        foreach ($this->getAllowedMethods() as $methodKey => $method) {
            $result->append(
                $this->createResultMethod($method)
            );
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        $allowedMethods = [];
        $quoteItems = $this->getQuoteItems();

        foreach ($this->methods as $method) {
            $method->setItems($quoteItems);
            if ($method->isAllowed()
            ) {
                $allowedMethods[] = [
                    'key' => $method->getKey(),
                    'sort' => $this->configProvider->getConfigData(
                        $this->_code . '/' . $method->getKey() . '/position'
                    ),
                    'price' => $method->calculatePrice()
                ];
            }
        }

        $sort = array_column($allowedMethods, "sort");
        array_multisort($sort, SORT_ASC, $allowedMethods);

        return $allowedMethods;
    }

    private function createResultMethod($method)
    {
        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $rateMethod */
        $rateMethod = $this->rateMethodFactory->create();

        $rateMethod->setCarrier($this->_code);
        $rateMethod->setCarrierTitle($this->configProvider->getConfigData($this->_code . '/label'));

        $rateMethod->setMethod($this->_code);
        $rateMethod->setMethodTitle($this->configProvider->getConfigData($method['key'] . '/name'));

        $rateMethod->setPrice($method['price']);
        $rateMethod->setCost($method['price']);
        return $rateMethod;
    }

    /**
     * @return mixed
     */
    protected function getQuoteItems()
    {
        return $this->checkoutSessionFactory->create->getQuote()->getAllVisibleItems();
    }
}
