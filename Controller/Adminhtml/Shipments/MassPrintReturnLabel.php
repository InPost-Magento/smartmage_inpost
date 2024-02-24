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
use Smartmage\Inpost\Model\ApiShipx\Service\Document\Printout\ReturnLabels as PrintoutReturnLabels;
use Smartmage\Inpost\Model\Config\Source\LabelFormat;
use Smartmage\Inpost\Model\ConfigProvider;
use Smartmage\Inpost\Model\ResourceModel\Shipment\CollectionFactory;

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

    /**
     * MassPrintReturnLabel constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param ConfigProvider $configProvider
     * @param PrintoutReturnLabels $printoutReturnLabels
     * @param FileFactory $fileFactory
     * @param DateTime $dateTime
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        ConfigProvider $configProvider,
        PrintoutReturnLabels $printoutReturnLabels,
        FileFactory $fileFactory,
        DateTime $dateTime,
        PsrLoggerInterface $logger
    ) {
        $this->logger = $logger;
        parent::__construct($context, $filter, $collectionFactory, $configProvider);
        $this->printoutReturnLabels = $printoutReturnLabels;
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

        $labelFormat = $this->configProvider->getLabelFormat();
        $labelSize = $this->configProvider->getLabelSize();

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

        $labelsData = [
            'ids' => $shipmentIds,
            LabelFormat::STRING_FORMAT => $labelFormat,
            LabelFormat::STRING_SIZE => $labelSize,
        ];

        try {
            if (!empty($shipmentIds)) {
                $result = $this->printoutReturnLabels->getLabels($labelsData);

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
