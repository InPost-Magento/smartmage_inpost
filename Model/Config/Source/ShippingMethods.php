<?php
declare(strict_types=1);

namespace Smartmage\Inpost\Model\Config\Source;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Shipping\Model\Config;
use Magento\Store\Model\ScopeInterface;

class ShippingMethods implements OptionSourceInterface
{
    const INPOST_MAPPER = [
        'inpostlocker_standard' => 'inpost_locker_standard',
        'inpostlocker_standardcod' => 'inpost_locker_standard',
        'inpostlocker_standardeow' => 'inpost_locker_standard',
        'inpostlocker_standardeowcod' => 'inpost_locker_standard',
        'inpostlocker_economic' => 'inpost_locker_standard',
        'inpostlocker_economiccod' => 'inpost_locker_standard',
        'inpostcourier_standard' => 'inpost_courier_standard',
        'inpostcourier_standardcod' => 'inpost_courier_standard',
        'inpostcourier_c2c' => 'inpost_courier_c2c',
        'inpostcourier_c2ccod' => 'inpost_courier_c2c',
        'inpostcourier_express1000' => 'inpost_courier_express_1000',
        'inpostcourier_express1200' => 'inpost_courier_express_1200',
        'inpostcourier_express1700' => 'inpost_courier_express_1700',
        'inpostcourier_palette' => 'inpost_courier_palette',
    ];

    const INPOST_CARRIER_CODES = ['inpostlocker', 'inpostcourier'];

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Shipping\Model\Config
     */
    protected $shippingConfig;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Shipping\Model\Config $shippingConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Config $shippingConfig
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->shippingConfig = $shippingConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray($isActiveOnlyFlag = false, $onlyMethodTitle = false) : array
    {
        $methods = [['value' => '', 'label' => '']];
        $carriers = $this->shippingConfig->getAllCarriers();
        foreach ($carriers as $carrierCode => $carrierModel) {
            if ((!$carrierModel->isActive() && (bool)$isActiveOnlyFlag === true)
                || !in_array($carrierCode, self::INPOST_CARRIER_CODES)
            ) {
                continue;
            }

            $carrierMethods = $carrierModel->getAllowedMethods($isActiveOnlyFlag);

            if (!$carrierMethods) {
                continue;
            }
            $carrierTitle = $this->scopeConfig->getValue(
                'carriers/' . $carrierCode . '/title',
                ScopeInterface::SCOPE_STORE
            );
            foreach ($carrierMethods as $methodCode => $methodTitle) {
                /** Check it $carrierMethods array was well formed */
                if (!$methodCode) {
                    continue;
                }

                $label = ($onlyMethodTitle) ? $methodTitle : '[' . $carrierTitle . '] ' . $methodTitle;
                $methods[] = [
                    'value' => $carrierCode . '_' . $methodCode,
                    'label' => $label,
                ];
            }
        }

        return $methods;
    }

    /**
     * @param string $magentoMethodName
     * @return string
     */
    public function getInpostMethod(string $magentoMethodName) : string
    {
        if (isset(self::INPOST_MAPPER[$magentoMethodName])) {
            return self::INPOST_MAPPER[$magentoMethodName];
        }
        return '';
    }
}
