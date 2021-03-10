<?php

namespace Smartmage\Inpost\Controller\Adminhtml\Shipments;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
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
     * @param FileFactory $fileFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context, $fileFactory);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        return $resultPage;
    }
}
