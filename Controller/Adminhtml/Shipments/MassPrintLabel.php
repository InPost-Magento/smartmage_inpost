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
use Smartmage\Inpost\Service\FileService;

/**
 * Class MassPrintLabel
 * Allow printing many InPost shipment labels
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
    private FileService $fileService;

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
        DateTime $dateTime,
        FileService $fileService
    ) {
        parent::__construct($context, $filter, $collectionFactory, $configProvider);
        $this->printoutLabels = $printoutLabels;
        $this->fileFactory = $fileFactory;
        $this->dateTime = $dateTime;
        $this->logger = $logger;
        $this->fileService = $fileService;
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
        $shipmentServices = $collection->getColumnValues('shipping_method');

        $services = array_count_values($collection->getColumnValues('service'));

        $labelFormat = $this->configProvider->getLabelFormat();

        try {
            $results = $this->printoutLabels->getLabels($shipmentIds, $shipmentServices);
            $files = [];

            if(count($results['files']) > 1) {
                foreach($results['files'] as $result) {
                    $files[] = $result[CallResult::STRING_FILE];
                }
                $filePath = $this->fileService->createZip($files, $results['format']);
                $resultData = [
                    $filePath,
                    LabelFormat::ZIP,
                    'filename'
                ];
            } else {
                $resultData = [
                    $results['files'][0][CallResult::STRING_FILE],
                    $results['format'],
                    'string'
                ];
            }

            return $this->fileService->generateFile(
                $resultData[0],
                $resultData[1],
                $resultData[2]
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
