<?php

namespace Smartmage\Inpost\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\RepositoryFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Checkout\Model\ConfigProviderInterface;
use Smartmage\Inpost\Model\Config\Source\ShippingMethods;

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
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var ShippingMethods
     */
    private $shippingMethods;

    /**
     * @var RepositoryFactory
     */
    private $repositoryFactory;

    /**
     * ConfigProvider constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param EncryptorInterface $encryptor
     * @param RepositoryFactory $repositoryFactory
     * @param ShippingMethods $shippingMethods
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        EncryptorInterface $encryptor,
        RepositoryFactory $repositoryFactory,
        ShippingMethods $shippingMethods
    ) {
        $this->encryptor = $encryptor;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->repositoryFactory = $repositoryFactory;
        $this->shippingMethods = $shippingMethods;
    }

    /**
     * @param $field
     * @return mixed
     * @throws NoSuchEntityException
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
     * @throws NoSuchEntityException
     */
    public function getMode()
    {
        return $this->getShippingConfigData(self::SHIPPING_MODE);
    }


    /**
     * @throws NoSuchEntityException
     */
    public function getDebugEnabled()
    {
        return $this->getShippingConfigData(self::DEBUG_ENABLED);
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getOrganizationId()
    {
        return $this->getShippingConfigData(self::SHIPPING_ORGANIZATION_ID);
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getAccessToken()
    {
        return $this->encryptor->decrypt($this->getShippingConfigData(self::SHIPPING_ACCESS_TOKEN));
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getLabelFormat()
    {
        return $this->getShippingConfigData(self::SHIPPING_LABEL_FORMAT);
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
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
     * @throws NoSuchEntityException
     */
    public function getChangeAddress()
    {
        return $this->getShippingConfigData(self::SHIPPING_CHANGE_ADDRESS);
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getSenderName()
    {
        return $this->getShippingConfigData(self::SHIPPING_SENDER_NAME);
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getSenderSurname()
    {
        return $this->getShippingConfigData(self::SHIPPING_SENDER_SURNAME);
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getSenderEmail()
    {
        return $this->getShippingConfigData(self::SHIPPING_SENDER_EMAIL);
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getSenderPhone()
    {
        return $this->getShippingConfigData(self::SHIPPING_SENDER_PHONE);
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getSenderStreet()
    {
        return $this->getShippingConfigData(self::SHIPPING_SENDER_STREET);
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getSenderBuildingNumber()
    {
        return $this->getShippingConfigData(self::SHIPPING_SENDER_BUILDING_NUMBER);
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getSenderCity()
    {
        return $this->getShippingConfigData(self::SHIPPING_SENDER_CITY);
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getSenderPostcode()
    {
        return $this->getShippingConfigData(self::SHIPPING_SENDER_POSTCODE);
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getSenderCountryCode()
    {
        return $this->getShippingConfigData(self::SHIPPING_SENDER_COUNTRY_CODE);
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getSenderCompany()
    {
        return $this->getShippingConfigData(self::SHIPPING_SENDER_COMPANY_NAME);
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getBecomePartner()
    {
        return $this->getShippingConfigData(self::SHIPPING_BECOME_PARTNER);
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
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
     * @throws NoSuchEntityException
     */
    public function getWeightAttributeCode()
    {
        return $this->getShippingConfigData(self::SHIPPING_WEIGHT_ATTRIBUTE_CODE);
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getWeightUnit()
    {
        return $this->getShippingConfigData(self::SHIPPING_WEIGHT_UNIT);
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getAutomaticInsuranceForPackage()
    {
        return $this->getShippingConfigData(self::SHIPPING_AUTOMATIC_INSURANCE_FOR_PACKAGE);
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getGetShipmentsDays()
    {
        return $this->getShippingConfigData(self::SHIPPING_GET_SHIPMENTS_DAYS);
    }

    /**
     * @param $field
     * template: carrier_code/method_key/field_name
     * @return false|mixed
     * @throws NoSuchEntityException
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
     * @throws NoSuchEntityException
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

    /**
     * @throws NoSuchEntityException
     */
    public function getDefaultWeight()
    {
        return $this->getShippingConfigData(self::DEFAULT_WEIGHT);
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getDefaultSize()
    {
        return $this->getShippingConfigData(self::DEFAULT_SIZE);
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getDefaultInsuranceValue()
    {
        return $this->getShippingConfigData(self::DEFAULT_INSURANCE_VALUE);
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getDefaultWidth()
    {
        return $this->getShippingConfigData(self::DEFAULT_WIDTH);
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getDefaultLength()
    {
        return $this->getShippingConfigData(self::DEFAULT_LENGTH);
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getDefaultHeight()
    {
        return $this->getShippingConfigData(self::DEFAULT_HEIGHT);
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getDefaultPickupStreet()
    {
        return $this->getShippingConfigData(self::SHIPPING_PICKUP_STREET);
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getDefaultPickupBuildingNumber()
    {
        return $this->getShippingConfigData(self::SHIPPING_PICKUP_BUILDING_NUMBER);
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getDefaultPickupCity()
    {
        return $this->getShippingConfigData(self::SHIPPING_PICKUP_CITY);
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getDefaultPickupPostCode()
    {
        return $this->getShippingConfigData(self::SHIPPING_PICKUP_POST_CODE);
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getDefaultPickupCountryCode()
    {
        return $this->getShippingConfigData(self::SHIPPING_PICKUP_COUNTRY_CODE);
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getConfig()
    {
        $repository = $this->repositoryFactory->create();
        $paczkomatDefaultLogo = $repository->getUrl('Smartmage_Inpost::images/inpost_paczkomat_logo.png');
        $courierDefaultLogo = $repository->getUrl('Smartmage_Inpost::images/inpost_kurier_logo.png');
        $listOfLogos = [];

        foreach ($this->shippingMethods::INPOST_MAPPER as $key => $value) {
            $configKey = str_replace('_','/', $key) . '/logo';
            $keyArray = explode('_',$key);
            $logoKey = $keyArray[0] . '_' . $keyArray[1] . '_' . $keyArray[0];
            if ($configValue = $this->getConfigData($configKey)) {
                $listOfLogos[$logoKey] = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . 'inpost_logo/' . $configValue;
            } elseif(strpos($key,'inpostlocker') !== false) {
                $listOfLogos[$logoKey] = $paczkomatDefaultLogo;
            } else {
                $listOfLogos[$logoKey] = $courierDefaultLogo;
            }
        }

        return array_merge($listOfLogos, [
            'standard_inpostlocker' => ($this->getConfigData('inpostlocker/standard/popenabled')) ? 'parcel_locker-pop' : 'parcel_locker',
            'geowidget_token' => $this->getShippingConfigData('geowidget_token'),
            'base_url' => $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_LINK)
        ]);
    }
}
