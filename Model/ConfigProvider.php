<?php

namespace Smartmage\Inpost\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class ConfigProvider
{
    const LABEL_FORMAT = 'label_format';
    const LABEL_SIZE = 'label_size';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * ConfigProvider constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * @param $field
     * template: carrier_code/method_key/field_name
     * @return false|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getConfigData($field)
    {
        $path = 'carriers/' . $field;

        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()
        );
    }

    /**
     * @param $field
     * template: carrier_code/method_key/field_name
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getConfigFlag($field)
    {
        $path = 'carriers/' . $field;

        return $this->scopeConfig->isSetFlag(
            $path,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()
        );
    }

    public function getShippingConfigData($field)
    {
        $path = 'shipping/inpost/' . $field;

        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()
        );
    }

    public function getLabelFormat()
    {
        return $this->getShippingConfigData(self::LABEL_FORMAT);
    }

    public function getLabelSize()
    {
        return $this->getShippingConfigData(self::LABEL_SIZE);
    }

}
