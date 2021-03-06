<?php

namespace Smartmage\Inpost\Model\Carrier;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Rate\Result;
use Smartmage\Inpost\Model\ConfigProvider;

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

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        array $methods,
        ConfigProvider $configProvider,
        array $data = []
    ) {
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->methods = $methods;
        $this->configProvider = $configProvider;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * @param RateRequest $request
     * @return bool|\Magento\Framework\DataObject|void|null
     */
    public function collectRates(RateRequest $request)
    {
        /** @var Result $result */
        $result = $this->rateResultFactory->create();

        foreach ($this->getAllowedMethods() as $methodKey => $method) {
            $this->createResultMethod($method);
        }

        /*
        $shippingPrice = $this->getShippingPrice($request, $freeBoxes);

        if ($shippingPrice !== false) {
            $method = $this->createResultMethod($shippingPrice);
            $result->append($method);
        }
        */

        return $result;
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        //'carriers/' . $this->_code . '/' . $field;
        //carriers/inpost/inpostcourier/c2c/active
        $allowedMethods = [];

        foreach ($this->methods as $method) {
            if ($this->configProvider->getConfigFlag($this->_code.'/'.$method::METHOD_KEY . '/active')
                && $method->isAllowed()
            ) {
                $allowedMethods[] = [
                    'key' => $method::METHOD_KEY,
                    'sort' => $this->configProvider->getConfigData($this->_code.'/'.$method::METHOD_KEY . '/position'),
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
        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->rateMethodFactory->create();

        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->configProvider->getConfigData($this->_code.'/label'));

        $method->setMethod($this->_code);
        $method->setMethodTitle($this->configProvider->getConfigData($method->getKey(). '/name'));

        $shippingPrice = $method->calculatePrice();
        $method->setPrice($shippingPrice);
        $method->setCost($shippingPrice);
        return $method;
    }
}
