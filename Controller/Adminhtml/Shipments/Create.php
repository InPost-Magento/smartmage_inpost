<?php

namespace Smartmage\Inpost\Controller\Adminhtml\Shipments;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Create extends \Smartmage\Inpost\Controller\Adminhtml\Shipments
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        return $resultPage;
    }
}
