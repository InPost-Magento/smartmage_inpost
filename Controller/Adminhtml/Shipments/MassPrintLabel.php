<?php
declare(strict_types=1);
namespace Smartmage\Inpost\Controller\Adminhtml\Shipments;

use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Ui\Component\MassAction\Filter;
use Smartmage\Inpost\Model\ApiShipx\CallResult;
use Smartmage\Inpost\Model\ApiShipx\Service\Document\Printout\Labels as PrintoutLabels;
use Smartmage\Inpost\Model\Config\Source\LabelFormat;
use Smartmage\Inpost\Model\ConfigProvider;
use Smartmage\Inpost\Model\ResourceModel\Shipment\CollectionFactory;

/**
 * Class MassPrintLabel
 * @package Smartmage\Inpost\Controller\Adminhtml\Shipments
 */
class MassPrintLabel extends MassActionAbstract
{
    protected $printoutLabels;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var PsrLoggerInterface
     */
    protected $logger;

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function __construct(
        PsrLoggerInterface $logger,
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        ConfigProvider $configProvider,
        PrintoutLabels $printoutLabels,
        FileFactory $fileFactory,
        DateTime $dateTime
    ) {
        parent::__construct($context, $filter, $collectionFactory, $configProvider);
        $this->printoutLabels = $printoutLabels;
        $this->fileFactory = $fileFactory;
        $this->dateTime = $dateTime;
        $this->logger = $logger;
    }

    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            throw new \Magento\Framework\Exception\NotFoundException(__('Page not found.'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $shipmentIds = $collection->getColumnValues('shipment_id');

        $services = array_count_values($collection->getColumnValues('service'));

        $labelFormat = $this->configProvider->getLabelFormat();
        $labelSize = $this->configProvider->getLabelSize();

        $labelsData = [
            'ids' => $shipmentIds,
            LabelFormat::STRING_FORMAT => $labelFormat,
            LabelFormat::STRING_SIZE => $labelSize,
        ];

        $this->logger->info(print_r('$labelsData', true));
        $this->logger->info(print_r($labelsData, true));

        try {
            $result = $this->printoutLabels->getLabels($labelsData);

            if (count($services) > 1) {
                $labelFormat = 'zip';
            }

            $fileContent = ['type' => 'string', 'value' => $result[CallResult::STRING_FILE], 'rm' => true];

            return $this->fileFactory->create(
                sprintf('labels-%s.' . $labelFormat, $this->dateTime->date('Y-m-d_H-i-s')),
                $fileContent,
                DirectoryList::VAR_DIR,
                LabelFormat::LABEL_CONTENT_TYPES[$labelFormat]
            );
        } catch (\Exception $e) {
            $this->logger->info(print_r($e->getMessage(), true));

            $this->messageManager->addExceptionMessage(
                $e
            );
        }

        return $resultRedirect->setPath('*/*/');
    }
}
