<?php
namespace Smartmage\Inpost\Controller\Locker;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultFactory;
use Smartmage\Inpost\Model\Checkout\Processor;

/**
 * Class Save
 */
class Save extends \Magento\Framework\App\Action\Action
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var Processor
     */
    protected $checkoutProcessor;

    /**
     * Save constructor.
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param Processor $checkoutProcessor
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        Processor $checkoutProcessor
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->checkoutProcessor = $checkoutProcessor;
        return parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        if ($this->getRequest()->isAjax()) {
            $lockerId = $this->getRequest()->getParam('inpost_locker_id');

            if ($lockerId) {
                if ($this->checkoutProcessor->setLockerId($lockerId)) {
                    $response = [
                        'status' => 1
                    ];
                } else {
                    $response = [
                        'status' => 0
                    ];
                }
            }

            return $result->setData($response);
        }

        /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
        $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
        $resultForward->forward('noroute');
        return $resultForward;
    }
}
