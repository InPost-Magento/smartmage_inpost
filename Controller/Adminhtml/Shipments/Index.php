<?php

namespace Smartmage\Inpost\Controller\Adminhtml\Shipments;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Response\Http\FileFactory;

class Index extends \Smartmage\Inpost\Controller\Adminhtml\Shipments
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * Lowstock constructor.
     * @param Context $context
     * @param FileFactory $fileFactory
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Action\Context $context,
        FileFactory $fileFactory,
        Registry $coreRegistry,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context, $fileFactory);
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Smartmage_Inpost::shipmentsmenu');
        $resultPage->getConfig()->getTitle()->prepend(__('Smartmage Inpost Shipments'));

        return $resultPage;
    }
}
