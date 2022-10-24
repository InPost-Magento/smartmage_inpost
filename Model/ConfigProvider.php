<?php

namespace Smartmage\Inpost\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Checkout\Model\ConfigProviderInterface;

class ConfigProvider implements ConfigProviderInterface
{
    const SHIPPING_MODE = 'mode';
    const DEBUG_ENABLED = 'debug_enabled';
    const SHIPPING_ORGANIZATION_ID = 'organization_id';
    const SHIPPING_ACCESS_TOKEN = 'access_token';
    const SHIPPING_LABEL_FORMAT = 'label_format';
    const SHIPPING_LABEL_SIZE = 'label_size';
    const SHIPPING_CHANGE_ADDRESS = 'change_address';
    const SHIPPING_SENDER_NAME = 'sender_name';
    const SHIPPING_SENDER_SURNAME = 'sender_surname';
    const SHIPPING_SENDER_EMAIL = 'sender_email';
    const SHIPPING_SENDER_PHONE = 'sender_phone';
    const SHIPPING_SENDER_STREET = 'sender_street';
    const SHIPPING_SENDER_BUILDING_NUMBER = 'sender_building_number';
    const SHIPPING_SENDER_CITY = 'sender_city';
    const SHIPPING_SENDER_POSTCODE = 'sender_postcode';
    const SHIPPING_SENDER_COUNTRY_CODE = 'sender_country_code';
    const SHIPPING_BECOME_PARTNER = 'become_partner';
    const SHIPPING_SZYBKIEZWROTY_URL = 'szybkiezwroty_url';
    const SHIPPING_WEIGHT_ATTRIBUTE_CODE = 'weight_attribute_code';
    const SHIPPING_WEIGHT_UNIT = 'weight_unit';
    const SHIPPING_AUTOMATIC_INSURANCE_FOR_PACKAGE = 'automatic_insurance_for_package';
    const SHIPPING_DEFAULT_PICKUP_POINT = 'default_pickup_pont';
    const SHIPPING_GET_SHIPMENTS_DAYS = 'get_shipments_days';
    const SHIPPING_LABEL_SIZE_PDF = 'label_size_pdf';
    const SHIPPING_LABEL_SIZE_EPL = 'label_size_epl';
    const SHIPPING_LABEL_SIZE_ZPL = 'label_size_zpl';
    const PDF = 'pdf';
    const EPL = 'epl';
    const ZPL = 'zpl';
    const DEFAULT_WEIGHT = 'default_weight';
    const DEFAULT_SIZE = 'default_size';
    const DEFAULT_INSURANCE_VALUE = 'default_insurance_value';
    const DEFAULT_WIDTH = 'default_width';
    const DEFAULT_HEIGHT = 'default_height';
    const DEFAULT_LENGTH = 'default_length';
    const SHIPPING_SENDER_COMPANY_NAME = 'sender_company';
    const SHIPPING_PICKUP_STREET = 'pickup_street';
    const SHIPPING_PICKUP_BUILDING_NUMBER = 'pickup_building_number';
    const SHIPPING_PICKUP_CITY = 'pickup_city';
    const SHIPPING_PICKUP_POST_CODE = 'pickup_post_code';
    const SHIPPING_PICKUP_COUNTRY_CODE = 'pickup_country_code';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $encryptor;

    /**
     * ConfigProvider constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        EncryptorInterface $encryptor
    ) {
        $this->encryptor = $encryptor;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * @param $field
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getShippingConfigData($field)
    {
        $path = 'shipping/inpost/' . $field;

        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()
        );
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMode()
    {
        return $this->getShippingConfigData(self::SHIPPING_MODE);
    }


    public function getDebugEnabled()
    {
        return $this->getShippingConfigData(self::DEBUG_ENABLED);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getOrganizationId()
    {
        return $this->getShippingConfigData(self::SHIPPING_ORGANIZATION_ID);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAccessToken()
    {
        return $this->encryptor->decrypt($this->getShippingConfigData(self::SHIPPING_ACCESS_TOKEN));
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getLabelFormat()
    {
        return $this->getShippingConfigData(self::SHIPPING_LABEL_FORMAT);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getLabelSize()
    {
        $labelFormat = $this->getLabelFormat();

        switch ($labelFormat) {
            case (self::PDF):
                return $this->getShippingConfigData(self::SHIPPING_LABEL_SIZE_PDF);
            case (self::EPL):
                return $this->getShippingConfigData(self::SHIPPING_LABEL_SIZE_EPL);
            case (self::ZPL):
                return $this->getShippingConfigData(self::SHIPPING_LABEL_SIZE_ZPL);
            default:
                return null;
        }
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getChangeAddress()
    {
        return $this->getShippingConfigData(self::SHIPPING_CHANGE_ADDRESS);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSenderName()
    {
        return $this->getShippingConfigData(self::SHIPPING_SENDER_NAME);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSenderSurname()
    {
        return $this->getShippingConfigData(self::SHIPPING_SENDER_SURNAME);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSenderEmail()
    {
        return $this->getShippingConfigData(self::SHIPPING_SENDER_EMAIL);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSenderPhone()
    {
        return $this->getShippingConfigData(self::SHIPPING_SENDER_PHONE);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSenderStreet()
    {
        return $this->getShippingConfigData(self::SHIPPING_SENDER_STREET);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSenderBuildingNumber()
    {
        return $this->getShippingConfigData(self::SHIPPING_SENDER_BUILDING_NUMBER);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSenderCity()
    {
        return $this->getShippingConfigData(self::SHIPPING_SENDER_CITY);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSenderPostcode()
    {
        return $this->getShippingConfigData(self::SHIPPING_SENDER_POSTCODE);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSenderCountryCode()
    {
        return $this->getShippingConfigData(self::SHIPPING_SENDER_COUNTRY_CODE);
    }

    public function getSenderCompany()
    {
        return $this->getShippingConfigData(self::SHIPPING_SENDER_COMPANY_NAME);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBecomePartner()
    {
        return $this->getShippingConfigData(self::SHIPPING_BECOME_PARTNER);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSzybkiezwrotyUrl()
    {
        if ($this->getShippingConfigData(self::SHIPPING_SZYBKIEZWROTY_URL)) {
            return $this->getShippingConfigData(self::SHIPPING_SZYBKIEZWROTY_URL);
        }
        return 'https://szybkiezwroty.pl/ ';
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getWeightAttributeCode()
    {
        return $this->getShippingConfigData(self::SHIPPING_WEIGHT_ATTRIBUTE_CODE);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getWeightUnit()
    {
        return $this->getShippingConfigData(self::SHIPPING_WEIGHT_UNIT);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAutomaticInsuranceForPackage()
    {
        return $this->getShippingConfigData(self::SHIPPING_AUTOMATIC_INSURANCE_FOR_PACKAGE);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getGetShipmentsDays()
    {
        return $this->getShippingConfigData(self::SHIPPING_GET_SHIPMENTS_DAYS);
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

    public function getDefaultWeight()
    {
        return $this->getShippingConfigData(self::DEFAULT_WEIGHT);
    }

    public function getDefaultSize()
    {
        return $this->getShippingConfigData(self::DEFAULT_SIZE);
    }

    public function getDefaultInsuranceValue()
    {
        return $this->getShippingConfigData(self::DEFAULT_INSURANCE_VALUE);
    }

    public function getDefaultWidth()
    {
        return $this->getShippingConfigData(self::DEFAULT_WIDTH);
    }

    public function getDefaultLength()
    {
        return $this->getShippingConfigData(self::DEFAULT_LENGTH);
    }

    public function getDefaultHeight()
    {
        return $this->getShippingConfigData(self::DEFAULT_HEIGHT);
    }

    public function getDefaultPickupStreet()
    {
        return $this->getShippingConfigData(self::SHIPPING_PICKUP_STREET);
    }

    public function getDefaultPickupBuildingNumber()
    {
        return $this->getShippingConfigData(self::SHIPPING_PICKUP_BUILDING_NUMBER);
    }

    public function getDefaultPickupCity()
    {
        return $this->getShippingConfigData(self::SHIPPING_PICKUP_CITY);
    }

    public function getDefaultPickupPostCode()
    {
        return $this->getShippingConfigData(self::SHIPPING_PICKUP_POST_CODE);
    }

    public function getDefaultPickupCountryCode()
    {
        return $this->getShippingConfigData(self::SHIPPING_PICKUP_COUNTRY_CODE);
    }

    public function getConfig()
    {
        return [
            'standard_inpostlocker' => ($this->getConfigData('inpostlocker/standard/popenabled')) ? 'parcel_locker-pop' : 'parcel_locker',
            'geowidget_token' => $this->getShippingConfigData('geowidget_token')
//            ,'mode' => $this->getShippingConfigData('mode')
        ];
    }
}
