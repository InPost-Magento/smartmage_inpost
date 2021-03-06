<?php

declare(strict_types=1);

namespace Smartmage\Inpost\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Smartmage\Inpost\Model\Carrier\Methods\Locker\Standard;
use Smartmage\Inpost\Model\Carrier\Methods\Locker\StandardCod;
use Smartmage\Inpost\Model\Carrier\Methods\Locker\StandardEow;
use Smartmage\Inpost\Model\Carrier\Methods\Locker\StandardEowCod;
use Psr\Log\LoggerInterface;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

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
     * @param ItemPriceCalculator $itemPriceCalculator
     * @param Standard $standardLocker
     * @param StandardCod $standardCod
     * @param StandardEow $standardEow
     * @param StandardEowCod $standardEowCod
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
            []
        );
    }
}
