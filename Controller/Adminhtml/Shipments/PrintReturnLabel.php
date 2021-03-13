<?php
declare(strict_types=1);
namespace Smartmage\Inpost\Controller\Adminhtml\Shipments;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\Result\RawFactory;

/**
 * Class PrintReturnLabel
 * @package Smartmage\Inpost\Controller\Adminhtml\Shipments
 */
class PrintReturnLabel extends Action
{
    protected $resultRawFactory;
    protected $fileFactory;

    /**
     * PrintReturnLabel constructor.
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        RawFactory $resultRawFactory,
        FileFactory $fileFactory,
        Context $context
    ) {
        $this->resultRawFactory      = $resultRawFactory;
        $this->fileFactory           = $fileFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        //todo connect with API
        $shipmentId = $this->getRequest()->getParam('id');
        $result['type'] = 'string';
        $result['value'] = 'cośtam return';
        $result['rm'] = true;
        return $this->fileFactory->create(
            'inpost_' . $shipmentId . '.csv',
            $result,
            DirectoryList::ROOT
        );
    }
}
