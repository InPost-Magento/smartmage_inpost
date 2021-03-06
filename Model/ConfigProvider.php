<?php

namespace Smartmage\Inpost\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class ConfigProvider
{
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
        $path = 'carriers/inpost/' . $field;

        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()
        );
        //carriers/inpost/inpostlocker/standard
    }

    /**
     * @param $field
     * template: carrier_code/method_key/field_name
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getConfigFlag($field)
    {
        $path = 'carriers/inpost/' . $field;

        return $this->scopeConfig->isSetFlag(
            $path,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()
        );
    }
}
