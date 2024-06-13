<?php

namespace Smartmage\Inpost\Controller\Adminhtml\Order;

use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Smartmage\Inpost\Model\ApiShipx\CallResult;
use Smartmage\Inpost\Model\ApiShipx\Service\Document\Printout\Labels;
use Smartmage\Inpost\Model\ApiShipx\Service\Shipment\MassCreate;
use Smartmage\Inpost\Model\Config\Source\LabelFormat;
use Smartmage\Inpost\Model\Config\Source\ShippingMethods;
use Smartmage\Inpost\Model\ConfigProvider;
use Smartmage\Inpost\Service\FileService;

class MassCreateAndPrintShipment extends Action
{

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Smartmage\Inpost\Model\ConfigProvider
     */
    protected $configProvider;

    /**
     * @var MassCreate
     */
    protected $massCreate;

    /**
     * @var Labels
     */
    protected $labels;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var ShippingMethods
     */
    protected $shippingMethods;

    /**
     * @var PsrLoggerInterface
     */
    protected $logger;
    private DirectoryList $directoryList;
    private FileService $fileService;

    /**
     * MassCreateAndPrintShipment constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param ConfigProvider $configProvider
     * @param MassCreate $massCreate
     * @param Labels $labels
     * @param FileFactory $fileFactory
     * @param DateTime $dateTime
     * @param ShippingMethods $shippingMethods
     * @param PsrLoggerInterface $logger
     * @param DirectoryList $directoryList
     * @param FileService $fileService
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        ConfigProvider $configProvider,
        MassCreate $massCreate,
        Labels $labels,
        FileFactory $fileFactory,
        DateTime $dateTime,
        ShippingMethods $shippingMethods,
        PsrLoggerInterface $logger,
        DirectoryList $directoryList,
        FileService $fileService
    ) {
        $this->logger = $logger;
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->configProvider = $configProvider;
        $this->massCreate = $massCreate;
        $this->labels = $labels;
        $this->fileFactory = $fileFactory;
        $this->dateTime = $dateTime;
        $this->shippingMethods = $shippingMethods;
        $this->directoryList = $directoryList;
        $this->fileService = $fileService;
    }

    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            throw new \Magento\Framework\Exception\NotFoundException(__('Page not found.'));
        }

        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $services = [];

        foreach ($collection as $item) {
            $services[$this->shippingMethods->getInpostMethod($item->getShippingMethod())] = 1;
        }

        $messages = $this->massCreate->createShipments($collection);

        if ($messages['success']) {
            $this->messageManager->addSuccessMessage($messages['success']);
        }

        if ($messages['notInpost']) {
            $this->messageManager->addWarningMessage($messages['notInpost']);
        }

        if ($messages['error']) {
            foreach ($messages['error'] as $message) {
                $this->messageManager->addComplexErrorMessage(
                    'errorInpostMassMessage',
                    [
                        'content' => $message,
                    ]
                );
            }
        }

        if (count($messages['shipmentIds']) > 0) {
            for ($x = 0; $x <= 10; $x++) {
                try {
                    if (!empty($messages['shipmentIds'])) {
                        $results = $this->labels->getLabels($messages['shipmentIds'], $messages['shipmentServices']);
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
                    }

                } catch (\Exception $e) {
                    $matches = [];
                    preg_match("/.+(\ .+<br>)$/", $e->getMessage(), $matches);

                    if (isset($matches[1]) &&
                        ($key = array_search(strip_tags(trim($matches[1])), $messages['shipmentIds']))
                        !== false
                    ) {
                        $this->logger->info(print_r($e->getMessage(), true));

                        $this->messageManager->addExceptionMessage(
                            $e
                        );
                        unset($messages['shipmentIds'][$key]);
                        continue;
                    }

                    $this->logger->info(print_r($e->getMessage(), true));

                    $this->messageManager->addExceptionMessage(
                        $e
                    );
                }
                sleep(2);
            }
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('sales/order/index');
    }
}
