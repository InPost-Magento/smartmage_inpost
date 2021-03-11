<?php

namespace Smartmage\Inpost\Controller\Adminhtml\Shipments;

use \Magento\Backend\App\Action;
use \Smartmage\Inpost\{
    Model\ApiShipx\Service\Shipment\Create\Courier,
    Model\ApiShipx\Service\Shipment\Create\Locker
};

class Save extends \Smartmage\Inpost\Controller\Adminhtml\Shipments
{

    protected $courier;

    protected $locker;

    public function __construct(
        Action\Context $context,
        Courier $courier,
        Locker $locker
    ) {
        $this->courier = $courier;
        $this->locker = $locker;
        parent::__construct($context);
    }

    /**
     * Update product attributes
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
//        if (!$this->_validateProducts()) {
//            return $this->resultRedirectFactory->create()->setPath('catalog/product/', ['_current' => true]);
//        }

        print_r('<pre>');
        print_r($this->getRequest()->getParams());
        print_r('</pre>');
        die();

        try {
            $this->courier->createShipment();
            $this->messageManager->addSuccessMessage(__('success'));
//        } catch (LocalizedException $e) {
//            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('error')
            );
        }

        return $this->resultRedirectFactory->create()->setPath('sales/order/view', ['order_id' => 1]);
    }

}
