<?php
namespace Smartmage\Inpost\Controller\Locker;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Smartmage\Inpost\Model\Checkout\Processor;
use Smartmage\Inpost\Model\ApiShipx\Service\Point\GetPoint;

class Get extends \Magento\Framework\App\Action\Action
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
     * @var GetPoint
     */
    protected $getPoint;

    /**
     * Get constructor.
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param Processor $checkoutProcessor
     * @param GetPoint $getPoint
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        Processor $checkoutProcessor,
        GetPoint $getPoint
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->checkoutProcessor = $checkoutProcessor;
        $this->getPoint = $getPoint;
        return parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Forward|Json|ResultInterface
     * @deprecated Since inpost_locker_id is taken from window.checkoutConfig.quoteData
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        if ($this->getRequest()->isAjax()) {
            $lockerId = $this->checkoutProcessor->getLockerId() ?? null;
            if (!$this->getPoint->isLockerExist($lockerId)) {
                $lockerId = null;
            }
            return $result->setData(['inpost_locker_id' => $lockerId]);
        }

        /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
        $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
        $resultForward->forward('noroute');
        return $resultForward;
    }
}
