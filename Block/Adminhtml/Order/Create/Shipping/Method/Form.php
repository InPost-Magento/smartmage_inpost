<?php
namespace Smartmage\Inpost\Block\Adminhtml\Order\Create\Shipping\Method;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Session\Quote;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Block\Adminhtml\Order\Create\Shipping\Method\Form as OriginalForm;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\AdminOrder\Create;
use Magento\Tax\Helper\Data;
use Smartmage\Inpost\Model\ConfigProvider;

class Form extends OriginalForm
{
    public ScopeConfigInterface $scopeConfig;

    /**
     * @param Context $context
     * @param Quote $sessionQuote
     * @param ScopeConfigInterface $configProvider
     * @param Create $orderCreate
     * @param PriceCurrencyInterface $priceCurrency
     * @param Data $taxData
     * @param array $data
     */
    public function __construct(
        Context                $context,
        Quote                  $sessionQuote,
        ConfigProvider         $configProvider,
        Create                 $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        Data                   $taxData,
        array                  $data = []
    ) {
        $this->configProvider = $configProvider;
        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $taxData);
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getInPostToken()
    {
        return $this->configProvider->getShippingConfigData('geowidget_token');
    }
}
