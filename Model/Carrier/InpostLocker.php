<?php

declare(strict_types=1);

namespace Smartmage\Inpost\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;
use Smartmage\Inpost\Model\Carrier\Methods\Locker\Standard;
use Smartmage\Inpost\Model\Carrier\Methods\Locker\StandardCod;
use Smartmage\Inpost\Model\Carrier\Methods\Locker\StandardEow;
use Smartmage\Inpost\Model\Carrier\Methods\Locker\StandardEowCod;
use Psr\Log\LoggerInterface;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Smartmage\Inpost\Model\ConfigProvider;
use Magento\Checkout\Model\Session;

/**
 * Class InpostLocker for locker carrier
 */
class InpostLocker extends AbstractInpostCarrier implements CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'inpostlocker';

    /**
     * InpostLocker constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param Standard $standardLocker
     * @param StandardCod $standardCod
     * @param StandardEow $standardEow
     * @param StandardEowCod $standardEowCod
     * @param Session $checkoutSession
     * @param ConfigProvider $configProvider
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        Standard $standardLocker,
        StandardCod $standardCod,
        StandardEow $standardEow,
        StandardEowCod $standardEowCod,
        Session $checkoutSession,
        ConfigProvider $configProvider,
        array $data = []
    ) {
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $methods = [
            $standardLocker,
            $standardCod,
            $standardEow,
            $standardEowCod
        ];
        parent::__construct(
            $scopeConfig,
            $rateErrorFactory,
            $logger,
            $rateResultFactory,
            $rateMethodFactory,
            $methods,
            $configProvider,
            $checkoutSession,
            []
        );
    }

    /**
     * @param RateRequest $request
     * @return Result
     */
    public function collectRates(RateRequest $request)
    {
        if ($this->disableNormalParcel()) {
            /** @var Result $result */
            $result = $this->rateResultFactory->create();

            $this->getAllowedMethods();

            foreach ($this->allowedMethods as $method) {

                if (!in_array($method['key'], $this->eowMethods) && $this->eowAvailable) {
                    continue;
                }
                $result->append(
                    parent::createResultMethod($method)
                );
            }

            return $result;
        } else {
            return parent::collectRates($request);
        }
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function disableNormalParcel()
    {
        if ($this->configProvider->getConfigFlag(
            $this->_code . '/show_only_delivery_eow'
        )) {
            return true;
        }

        return false;
    }
}
