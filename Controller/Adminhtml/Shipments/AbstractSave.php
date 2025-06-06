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
            'inpostlocker_economic' => $this->locker,
            'inpostlocker_economiccod' => $this->locker,
            'inpostcourier_standard' => $this->courier,
            'inpostcourier_standardcod' => $this->courier,
            'inpostcourier_c2c' => $this->courier,
            'inpostcourier_c2ccod' => $this->courier,
            'inpostcourier_express1000' => $this->courier,
            'inpostcourier_express1200' => $this->courier,
            'inpostcourier_express1700' => $this->courier,
            'inpostcourier_palette' => $this->courier,
            'inpostcourier_alcohol' => $this->courier,
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
        $data = $this->_request->getParams();

        try {
            $result = $this->processShippment();
            $this->messageManager->addSuccessMessage($result[CallResult::STRING_MESSAGE]);
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e
            );
            $data['shipment_fieldset']['shipping_method'] = $data['shipment_fieldset']['service'];
            return $this->resultRedirectFactory->create()
                ->setPath('smartmageinpost/shipments/create', $data['shipment_fieldset']);
        }

        return $this->resultRedirectFactory->create()
            ->setPath('sales/order/view', ['order_id' => $data['shipment_fieldset']['order_id']]);
    }
}
