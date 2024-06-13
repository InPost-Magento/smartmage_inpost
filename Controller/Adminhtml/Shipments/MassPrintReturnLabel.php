<?php
declare(strict_types=1);
namespace Smartmage\Inpost\Controller\Adminhtml\Shipments;

use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Ui\Component\MassAction\Filter;
use Smartmage\Inpost\Model\ApiShipx\CallResult;
use Smartmage\Inpost\Model\ApiShipx\Service\Document\Printout\ReturnLabels as PrintoutReturnLabels;
use Smartmage\Inpost\Model\Config\Source\LabelFormat;
use Smartmage\Inpost\Model\ConfigProvider;
use Smartmage\Inpost\Model\ResourceModel\Shipment\CollectionFactory;
use Smartmage\Inpost\Service\FileService;

class MassPrintReturnLabel extends MassActionAbstract
{
    protected $printoutReturnLabels;

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
     * MassPrintReturnLabel constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param ConfigProvider $configProvider
     * @param PrintoutReturnLabels $printoutReturnLabels
     * @param FileFactory $fileFactory
     * @param DateTime $dateTime
     * @param PsrLoggerInterface $logger
     * @param FileService $fileService
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        ConfigProvider $configProvider,
        PrintoutReturnLabels $printoutReturnLabels,
        FileFactory $fileFactory,
        DateTime $dateTime,
        PsrLoggerInterface $logger,
        FileService $fileService
    ) {
        $this->logger = $logger;
        parent::__construct($context, $filter, $collectionFactory, $configProvider);
        $this->printoutReturnLabels = $printoutReturnLabels;
        $this->fileFactory = $fileFactory;
        $this->dateTime = $dateTime;
        $this->fileService = $fileService;
    }

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
        $shipmentServices = $collection->getColumnValues('shipping_method');

        $shipmentIds = [];
        $omittedIds = [];
        $services = [];

        //etykieta zwrotna tylko dla usług kurierskich oraz nie dla C2C
        foreach ($collection as $item) {
            if (substr($item->getService(), 0, 14) === "inpost_courier" && !in_array($item->getService(), ['inpost_courier_c2c', 'inpost_courier_c2ccod'])) {
                $services[$item->getService()] = 1;
                $shipmentIds[] = $item->getShipmentId();
            } else {
                $omittedIds[] = $item->getShipmentId();
            }
        }

        try {
            if (!empty($shipmentIds)) {
                $results = $this->printoutReturnLabels->getLabels($shipmentIds, array_keys($services));
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
            } else {
                if (!empty($omittedIds)) {
                    $this->messageManager->addWarningMessage((count($omittedIds) > 1 ? __('Shipments') : __('Shipment'))
                        . ' ' . implode(', ', $omittedIds)
                        . ' ' . (count($omittedIds) > 1 ? __('have been omitted_m') : __('have been omitted_s')));
                }
            }

        } catch (\Exception $e) {
            $this->logger->info(print_r($e->getMessage(), true));
            $this->messageManager->addExceptionMessage($e);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
