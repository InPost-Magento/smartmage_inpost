<?php
declare(strict_types=1);

namespace Smartmage\Inpost\Controller\Adminhtml\Shipments;

use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Smartmage\Inpost\Model\ConfigProvider;
use Smartmage\Inpost\Model\ResourceModel\Shipment\CollectionFactory;
use Smartmage\Inpost\Model\Shipment;
use Smartmage\Inpost\Model\ShipmentRepository;

class MassDelete extends MassActionAbstract
{
    /**
     * Massactions filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var ShipmentRepository
     */
    private $shipmentRepository;

    /**
     * @var PsrLoggerInterface
     */
    private $logger;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param ConfigProvider $configProvider
     * @param PsrLoggerInterface $logger
     * @param ShipmentRepository $shipmentRepository
     */
    public function __construct(
        Context            $context,
        Filter             $filter,
        CollectionFactory  $collectionFactory,
        ConfigProvider     $configProvider,
        PsrLoggerInterface $logger,
        ShipmentRepository $shipmentRepository
    )
    {
        $this->logger = $logger;
        parent::__construct($context, $filter, $collectionFactory, $configProvider);
        $this->shipmentRepository = $shipmentRepository;
    }

    /**
     * Mass Delete Action
     *
     * @return Redirect
     * @throws LocalizedException
     */
    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            throw new NotFoundException(__('Page not found.'));
        }
        $collection = $this->filter->getCollection($this->collectionFactory->create());

        $shipmentDeleted = 0;
        $shipmentDeletedError = 0;
        /** @var Shipment $shipment */
        foreach ($collection->getItems() as $shipment) {
            try {
                $this->shipmentRepository->delete($shipment);
                $shipmentDeleted++;
            } catch (LocalizedException $exception) {
                $this->logger->error($exception->getLogMessage());
                $shipmentDeletedError++;
            }
        }

        if ($shipmentDeleted) {
            $this->messageManager->addSuccessMessage(
                __('A total of %1 record(s) have been deleted.', $shipmentDeleted)
            );
        }

        if ($shipmentDeletedError) {
            $this->messageManager->addErrorMessage(
                __(
                    'A total of %1 record(s) haven\'t been deleted. Please see server logs for more details.',
                    $shipmentDeletedError
                )
            );
        }

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }
}
