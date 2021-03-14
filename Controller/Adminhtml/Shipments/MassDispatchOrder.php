<?php
declare(strict_types=1);
namespace Smartmage\Inpost\Controller\Adminhtml\Shipments;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Ui\Component\MassAction\Filter;
use Smartmage\Inpost\Model\ConfigProvider;
use Smartmage\Inpost\Model\ResourceModel\Shipment\CollectionFactory;
use Smartmage\Inpost\Model\ApiShipx\Service\DispatchOrder\Create as DispatchOrderCreate;

/**
 * Class MassPrintLabel
 * @package Smartmage\Inpost\Controller\Adminhtml\Shipments
 */
class MassDispatchOrder extends MassActionAbstract
{

    /**
     * @var
     */
    protected $printoutLabels;

    /**
     * @var FileFactory
     */
    protected $fileFactory;
    /**
     * @var \Smartmage\Inpost\Model\ApiShipx\Service\DispatchOrder\Create
     */
    protected $dispatchOrderCreate;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * MassDispatchOrder constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Smartmage\Inpost\Model\ResourceModel\Shipment\CollectionFactory $collectionFactory
     * @param \Smartmage\Inpost\Model\ConfigProvider $configProvider
     * @param \Smartmage\Inpost\Model\ApiShipx\Service\DispatchOrder\Create $dispatchOrderCreate
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        ConfigProvider $configProvider,
        DispatchOrderCreate $dispatchOrderCreate
    ) {
        $this->dispatchOrderCreate = $dispatchOrderCreate;
        parent::__construct($context, $filter, $collectionFactory, $configProvider);
    }


    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\NotFoundException
     */
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
        $selectedIds = $collection->getColumnValues('shipment_id');

        $defaultPickupPoint = $this->configProvider->getDefaultPickupPoint();

        $dispatchData = [
            'shipments' => $selectedIds,
            'dispatch_point_id' => $defaultPickupPoint
        ];

        $logger->info(print_r('$labelsData', true));
        $logger->info(print_r($dispatchData, true));

        try {
            $this->dispatchOrderCreate->createDispatchOrders($dispatchData);

            $this->messageManager->addSuccessMessage(__('Dispatch order was created'));
        } catch (\Exception $e) {
            $logger->info(print_r($e->getMessage(), true));

            $this->messageManager->addExceptionMessage(
                $e
            );
        }

        return $resultRedirect->setPath('*/*/');
    }
}
