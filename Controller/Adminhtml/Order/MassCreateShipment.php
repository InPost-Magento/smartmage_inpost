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
     * MassCreateShipment constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param ConfigProvider $configProvider
     * @param MassCreate $massCreate
     * @param Labels $labels
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        ConfigProvider $configProvider,
        MassCreate $massCreate,
        Labels $labels,
        FileFactory $fileFactory,
        DateTime $dateTime
    ) {

        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->configProvider = $configProvider;
        $this->massCreate = $massCreate;
        $this->labels = $labels;
        $this->fileFactory = $fileFactory;
        $this->dateTime = $dateTime;
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
            foreach ($messages['error'] as $message) {
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
