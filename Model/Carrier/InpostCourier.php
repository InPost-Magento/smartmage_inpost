<?php

declare(strict_types=1);

namespace Smartmage\Inpost\Model\Carrier;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\ResultFactory;
use Psr\Log\LoggerInterface;
use Smartmage\Inpost\Model\Carrier\Methods\Courier\Alcohol;
use Smartmage\Inpost\Model\Carrier\Methods\Courier\C2c;
use Smartmage\Inpost\Model\Carrier\Methods\Courier\C2cCod;
use Smartmage\Inpost\Model\Carrier\Methods\Courier\Express1000;
use Smartmage\Inpost\Model\Carrier\Methods\Courier\Express1200;
use Smartmage\Inpost\Model\Carrier\Methods\Courier\Express1700;
use Smartmage\Inpost\Model\Carrier\Methods\Courier\Palette;
use Smartmage\Inpost\Model\Carrier\Methods\Courier\Standard;
use Smartmage\Inpost\Model\Carrier\Methods\Courier\StandardCod;
use Smartmage\Inpost\Model\ConfigProvider;

/**
 * Class InpostCourier for courier carrier
 */
class InpostCourier extends AbstractInpostCarrier implements CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'inpostcourier';

    /**
     * InpostCourier constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param C2c $c2c
     * @param C2cCod $c2cCod
     * @param Express1000 $express1000
     * @param Express1200 $express1200
     * @param Express1700 $express1700
     * @param Palette $palette
     * @param Standard $standard
     * @param StandardCod $standardCod
     * @param Alcohol $alcohol
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
        C2c $c2c,
        C2cCod $c2cCod,
        Express1000 $express1000,
        Express1200 $express1200,
        Express1700 $express1700,
        Palette $palette,
        Standard $standard,
        StandardCod $standardCod,
        Alcohol $alcohol,
        Session $checkoutSession,
        ConfigProvider $configProvider,
        array $data = []
    ) {
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $methods = [
            $standard,
            $standardCod,
            $express1000,
            $express1200,
            $express1700,
            $palette,
            $c2c,
            $c2cCod,
            $alcohol
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
}
