<?php

namespace Smartmage\Inpost\Controller\Adminhtml\Order;

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
        ShippingMethods $shippingMethods
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->configProvider = $configProvider;
        $this->massCreate = $massCreate;
        $this->labels = $labels;
        $this->fileFactory = $fileFactory;
        $this->dateTime = $dateTime;
        $this->shippingMethods = $shippingMethods;
    }

    public function execute()
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/inpost.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

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

        $labelFormat = $this->configProvider->getLabelFormat();
        $labelSize = $this->configProvider->getLabelSize();

        if (count($messages['shipment_ids']) > 0) {
            for ($x = 0; $x <= 10; $x++) {
                try {
                    if (!empty($messages['shipment_ids'])) {

                        if (count($services) > 1) {
                            $labelFormat = 'zip';
                        }

                        $labelsData = [
                            'ids' => $messages['shipment_ids'],
                            LabelFormat::STRING_FORMAT => $labelFormat,
                            LabelFormat::STRING_SIZE => $labelSize,
                        ];

                        $result = $this->labels->getLabels($labelsData);

                        $fileContent = ['type' => 'string', 'value' => $result[CallResult::STRING_FILE], 'rm' => true];

                        return $this->fileFactory->create(
                            sprintf('labels-%s.' . $labelFormat, $this->dateTime->date('Y-m-d_H-i-s')),
                            $fileContent,
                            DirectoryList::VAR_DIR,
                            LabelFormat::LABEL_CONTENT_TYPES[$labelFormat]
                        );
                    }

                } catch (\Exception $e) {
                    $matches = [];
                    preg_match("/.+(\ .+<br>)$/", $e->getMessage(), $matches);

                    if (isset($matches[1]) &&
                        ($key = array_search(strip_tags(trim($matches[1])), $messages['shipment_ids']))
                        !== false
                    ) {
                        $logger->info(print_r($e->getMessage(), true));

                        $this->messageManager->addExceptionMessage(
                            $e
                        );
                        unset($messages['shipment_ids'][$key]);
                        continue;
                    }

                    $logger->info(print_r($e->getMessage(), true));

                    $this->messageManager->addExceptionMessage(
                        $e
                    );
                }
            }
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('sales/order/index');
    }
}
