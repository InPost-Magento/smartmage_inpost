<?php

namespace Smartmage\Inpost\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Smartmage\Inpost\Model\ApiShipx\Service\Shipment\MassCreate;
use Smartmage\Inpost\Model\ConfigProvider;

class MassCreateShipment extends Action
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
     * MassCreateShipment constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param ConfigProvider $configProvider
     * @param MassCreate $massCreate
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        ConfigProvider $configProvider,
        MassCreate $massCreate
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->configProvider = $configProvider;
        $this->massCreate = $massCreate;
    }

    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            throw new \Magento\Framework\Exception\NotFoundException(__('Page not found.'));
        }

        $collection = $this->filter->getCollection($this->collectionFactory->create());

        $messages = $this->massCreate->createShipments($collection);

        if ($messages['success']) {
            $this->messageManager->addSuccessMessage($messages['success']);
        }

        if ($messages['notInpost']) {
            $this->messageManager->addWarningMessage($messages['notInpost']);
        }

        if ($messages['error']) {
            foreach($messages['error'] as $message) {
                $this->messageManager->addComplexErrorMessage(
                    'errorInpostMassMessage',
                    [
                        'content' => $message,
                    ]
                );
            }

        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('sales/order/index');
    }
}
