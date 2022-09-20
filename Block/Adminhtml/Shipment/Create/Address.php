<?php
namespace Smartmage\Inpost\Block\Adminhtml\Shipment\Create;

use Magento\Framework\Exception\NoSuchEntityException;

class Address extends \Magento\Backend\Block\Template
{
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'Smartmage_Inpost::shipment/create/address.phtml';

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getInPostToken()
    {
        return $this->_scopeConfig->getValue('shipping/inpost/geowidget_token');
    }
}
