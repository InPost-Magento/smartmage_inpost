<?php
namespace Smartmage\Inpost\Controller\Locker;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Smartmage\Inpost\Model\Checkout\Processor;
use Smartmage\Inpost\Model\ConfigProvider;

class GetMode extends Action
{

    const MODE_KEY = 'mode';

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var ConfigProvider
     */
    protected $configProvider;

    /**
     * GetMode constructor.
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param ConfigProvider $configProvider
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        ConfigProvider $configProvider
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->configProvider = $configProvider;
        return parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Forward|Json|ResultInterface
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        if ($this->getRequest()->isAjax()) {
            $mode = $this->configProvider->getMode();
            return $result->setData([self::MODE_KEY => $mode]);
        }

        /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
        $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
        $resultForward->forward('noroute');
        return $resultForward;
    }
}
