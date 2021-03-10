<?php
declare(strict_types=1);
namespace Smartmage\Inpost\Controller\Adminhtml\Shipments;

use Magento\Framework\Controller\ResultFactory;

/**
 * Class MassPrintReturnLabel
 * @package Smartmage\Inpost\Controller\Adminhtml\Shipments
 */
class MassPrintReturnLabel extends MassActionAbstract
{

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            throw new \Magento\Framework\Exception\NotFoundException(__('Page not found.'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $selectedIds = $collection->getAllIds();
        $labelFormat = $this->configProvider->getLabelFormat();

        $this->messageManager->addSuccess(__('A total of %1 record(s) have been selected(MassPrintReturnLabel).', count($selectedIds)));

        return $resultRedirect->setPath('*/*/');
    }
}
