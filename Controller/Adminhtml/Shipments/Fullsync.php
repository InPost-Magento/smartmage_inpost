<?php

namespace Smartmage\Inpost\Controller\Adminhtml\Shipments;

use \Magento\Backend\App\Action;
use \Smartmage\Inpost\{
    Model\ApiShipx\Service\Shipment\Search\Multiple as SearchMultiple
};

class Fullsync extends \Smartmage\Inpost\Controller\Adminhtml\Shipments
{

    protected $searchMultiple;

    public function __construct(
        Action\Context $context,
        SearchMultiple $searchMultiple
    ) {
        $this->searchMultiple = $searchMultiple;
        parent::__construct($context);
    }

    /**
     * Update product attributes
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {

        try {
            $this->searchMultiple->getAllShipments();
            $this->messageManager->addSuccessMessage(__('success'));
//        } catch (LocalizedException $e) {
//            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('error')
            );
        }

        return $this->resultRedirectFactory->create()->setPath('smartmageinpost/shipments/index');
    }

}
