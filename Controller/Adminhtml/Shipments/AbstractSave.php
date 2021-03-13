<?php

namespace Smartmage\Inpost\Controller\Adminhtml\Shipments;

use Magento\Backend\App\Action;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Smartmage\Inpost\Model\ApiShipx\CallResult;
use Smartmage\Inpost\Model\ApiShipx\Service\Shipment\Create\Courier;
use Smartmage\Inpost\Model\ApiShipx\Service\Shipment\Create\Locker;

abstract class AbstractSave extends Action
{
    protected $courier;

    protected $locker;

    protected $classMapper;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    public function __construct(
        Action\Context $context,
        Courier $courier,
        Locker $locker,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->courier = $courier;
        $this->locker = $locker;
        $this->orderRepository = $orderRepository;

        $this->classMapper = [
            'inpostlocker_standard' => $this->locker,
            'inpostlocker_standardcod' => $this->locker,
            'inpostlocker_standardeow' => $this->locker,
            'inpostlocker_standardeowcod' => $this->locker,
            'inpostcourier_standard' => $this->courier,
            'inpostcourier_c2c' => $this->courier,
            'inpostcourier_c2ccod' => $this->courier,
            'inpostcourier_express1000' => $this->courier,
            'inpostcourier_express1200' => $this->courier,
            'inpostcourier_express1700' => $this->courier,
            'inpostcourier_localstandard' => $this->courier,
            'inpostcourier_localexpress' => $this->courier,
            'inpostcourier_localsuperexpress' => $this->courier,
            'inpostcourier_palette' => $this->courier,
        ];

        parent::__construct($context);
    }

    /**
     * Update product attributes
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        try {
            $result = $this->processShippment();
            $this->messageManager->addSuccessMessage($result[CallResult::STRING_MESSAGE]);
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e
            );
        }

        return $this->resultRedirectFactory->create()->setPath('sales/order/view', ['order_id' => 1]);
    }
}
