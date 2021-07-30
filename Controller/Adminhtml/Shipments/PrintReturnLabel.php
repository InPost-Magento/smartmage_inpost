<?php
declare(strict_types=1);
namespace Smartmage\Inpost\Controller\Adminhtml\Shipments;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Smartmage\Inpost\Model\ApiShipx\CallResult;
use Smartmage\Inpost\Model\ApiShipx\Service\Document\Printout\ReturnLabels as PrintoutReturnLabels;
use Smartmage\Inpost\Model\Config\Source\LabelFormat;
use Smartmage\Inpost\Model\ConfigProvider;

/**
 * Class PrintReturnLabel
 * @package Smartmage\Inpost\Controller\Adminhtml\Shipments
 */
class PrintReturnLabel extends Action
{
    protected $resultRawFactory;
    protected $fileFactory;
    protected $configProvider;
    protected $printoutReturnLabels;
    protected $dateTime;

    public function __construct(
        FileFactory $fileFactory,
        Context $context,
        ConfigProvider $configProvider,
        PrintoutReturnLabels $printoutReturnLabels,
        DateTime $dateTime
    ) {
        $this->fileFactory           = $fileFactory;
        $this->configProvider      = $configProvider;
        $this->printoutReturnLabels  = $printoutReturnLabels;
        $this->dateTime           = $dateTime;
        parent::__construct($context);
    }


    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/inpost.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $shipmentId = $this->getRequest()->getParam('id');
        $labelFormat = $this->configProvider->getLabelFormat();
        $labelSize = $this->configProvider->getLabelSize();
        $labelsData = [
            'ids' => [$shipmentId],
            LabelFormat::STRING_FORMAT => $labelFormat,
            LabelFormat::STRING_SIZE => $labelSize,
        ];

        try {
            $result = $this->printoutReturnLabels->getLabels($labelsData);

            $fileContent = ['type' => 'string', 'value' => $result[CallResult::STRING_FILE], 'rm' => true];

            return $this->fileFactory->create(
                sprintf('labels-%s.' . $labelFormat, $this->dateTime->date('Y-m-d_H-i-s')),
                $fileContent,
                DirectoryList::VAR_DIR,
                LabelFormat::LABEL_CONTENT_TYPES[$labelFormat]
            );

        } catch (\Exception $e) {
            $logger->info(print_r($e->getMessage(), true));

            $this->messageManager->addExceptionMessage(
                $e
            );
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath(
            'sales/order/view',
            ['order_id' => $this->getRequest()->getParam('order_id')]
        );
    }
}
