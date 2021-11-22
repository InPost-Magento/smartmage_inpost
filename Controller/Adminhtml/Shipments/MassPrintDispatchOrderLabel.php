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
use Smartmage\Inpost\Model\ApiShipx\Service\Document\Printout\DispatchOrders as PrintoutDispatchOrders;
use Smartmage\Inpost\Model\Config\Source\LabelFormat;
use Smartmage\Inpost\Model\ConfigProvider;
use Smartmage\Inpost\Model\ResourceModel\Shipment\CollectionFactory;

/**
 * Class massPrintDispatchOrderLabel
 */
class MassPrintDispatchOrderLabel extends MassActionAbstract
{
    protected $printoutDispatchOrders;

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
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        ConfigProvider $configProvider,
        PrintoutDispatchOrders $printoutDispatchOrders,
        FileFactory $fileFactory,
        DateTime $dateTime,
        PsrLoggerInterface $logger
    ) {
        $this->logger = $logger;
        parent::__construct($context, $filter, $collectionFactory, $configProvider);
        $this->printoutDispatchOrders = $printoutDispatchOrders;
        $this->fileFactory = $fileFactory;
        $this->dateTime = $dateTime;
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


        $shipmentIds = [];
        $omittedIds = [];
        $services = [];

        foreach ($collection as $item) {
            if ($item->getSendingMethod() == 'dispatch_order') {
                $services[$item->getService()] = 1;
                $shipmentIds[] = $item->getShipmentId();
            } else {
                $omittedIds[] = $item->getShipmentId();
            }
        }

        $labelFormat = $this->configProvider->getLabelFormat();
        $labelSize = $this->configProvider->getLabelSize();

        $dispatchOrdersData = [
            'ids' => $shipmentIds,
            LabelFormat::STRING_FORMAT => $labelFormat,
            LabelFormat::STRING_SIZE => $labelSize,
        ];

        $this->logger->info(print_r('$labelsData', true));
        $this->logger->info(print_r($dispatchOrdersData, true));

        try {
            if (!empty($shipmentIds)) {
                $result = $this->printoutDispatchOrders->getDispatchOrders($dispatchOrdersData);

                $fileContent = ['type' => 'string', 'value' => $result[CallResult::STRING_FILE], 'rm' => true];

                if (count($services) > 1) {
                    $labelFormat = 'zip';
                }

                return $this->fileFactory->create(
                    sprintf('labels-%s.' . $labelFormat, $this->dateTime->date('Y-m-d_H-i-s')),
                    $fileContent,
                    DirectoryList::VAR_DIR,
                    LabelFormat::LABEL_CONTENT_TYPES[$labelFormat]
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
