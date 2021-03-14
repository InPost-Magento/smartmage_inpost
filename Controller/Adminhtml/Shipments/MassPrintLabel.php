<?php
declare(strict_types=1);
namespace Smartmage\Inpost\Controller\Adminhtml\Shipments;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Ui\Component\MassAction\Filter;
use Smartmage\Inpost\Api\Data\ShipmentInterface;
use Smartmage\Inpost\Api\ShipmentRepositoryInterface;
use Smartmage\Inpost\Model\ApiShipx\CallResult;
use Smartmage\Inpost\Model\ConfigProvider;
use Smartmage\Inpost\Model\ResourceModel\Shipment\CollectionFactory;
use Smartmage\Inpost\Model\ApiShipx\Service\Document\Printout\Labels as PrintoutLabels;

/**
 * Class MassPrintLabel
 * @package Smartmage\Inpost\Controller\Adminhtml\Shipments
 */
class MassPrintLabel extends MassActionAbstract
{

    protected $printoutLabels;

    /**
     * @var \Smartmage\Inpost\Api\ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var DateTime
     */
    protected $dateTime;

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
        PrintoutLabels $printoutLabels,
        ShipmentRepositoryInterface $shipmentRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FileFactory $fileFactory,
        DateTime $dateTime
    ) {
        parent::__construct($context, $filter, $collectionFactory, $configProvider);
        $this->printoutLabels = $printoutLabels;
        $this->shipmentRepository = $shipmentRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->fileFactory = $fileFactory;
        $this->dateTime = $dateTime;
    }

    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            throw new \Magento\Framework\Exception\NotFoundException(__('Page not found.'));
        }

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/inpost.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $selectedIds = $collection->getAllIds();

        $shipments = null;

        if (!empty($selectedIds)) {
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter(ShipmentInterface::ENTITY_ID, $selectedIds, 'in')
                ->create();

            $shipments = $this->shipmentRepository->getList($searchCriteria)->getItems();
        }

        $shipmentIds = [];
        foreach ($shipments as $shipment) {
            $shipmentIds[] = $shipment->getShipmentId();
        }

//        $labelFormat = $this->configProvider->getLabelFormat();
        $labelFormat = 'pdf';
        $labelSize = $this->configProvider->getLabelSize();

        $labelsData = [
            'ids' => $shipmentIds,
            'format' => $labelFormat,
            'size' => $labelSize,
        ];

        $logger->info(print_r('$labelsData', true));
        $logger->info(print_r($labelsData, true));

        try {
            $result = $this->printoutLabels->getLabels($labelsData);

            $fileContent = ['type' => 'string', 'value' => $result['file'], 'rm' => true];

            return $this->fileFactory->create(
                sprintf('labels-%s.pdf', $this->dateTime->date('Y-m-d_H-i-s')),
                $fileContent,
                DirectoryList::VAR_DIR,
                'application/pdf'
            );
//
//            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been selected.(MassPrintLabel)', count($selectedIds)));
        } catch (\Exception $e) {
            $logger->info(print_r($e->getMessage(), true));

            $this->messageManager->addExceptionMessage(
                $e
            );
        }

        return $resultRedirect->setPath('*/*/');
    }
}
