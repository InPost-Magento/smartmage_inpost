<?php
namespace Smartmage\Inpost\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Smartmage\Inpost\Model\ConfigProvider;

class InpostMode extends Template
{
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'Smartmage_Inpost::inpostMode.phtml';
    protected $configProvider;

    public function __construct(
        Context $context,
        ConfigProvider $configProvider,
        array $data = [],
        ?JsonHelper $jsonHelper = null,
        ?DirectoryHelper $directoryHelper = null
    ) {
        $this->configProvider = $configProvider;
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);
    }
    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getGeowidgetToken()
    {
        return $this->configProvider->getGeowidgetToken();
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getInPostMode(): string
    {
        return $this->_scopeConfig->getValue('shipping/inpost/mode');
    }
}
