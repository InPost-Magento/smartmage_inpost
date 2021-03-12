<?php

namespace Smartmage\Inpost\Controller\Adminhtml\Shipments;

use Magento\Backend\App\Action;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Smartmage\Inpost\Model\ApiShipx\Service\Shipment\Create\Courier;
use Smartmage\Inpost\Model\ApiShipx\Service\Shipment\Create\Locker;

class Save extends \Smartmage\Inpost\Controller\Adminhtml\Shipments
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
        $data = $this->getRequexst()->getParams();
        $shipmentClass = $this->classMapper[$data['shipment_fieldset']['service']];

        try {
            $shipmentClass->createBody(
                $data['shipment_fieldset'],
                $this->orderRepository->get($data['shipment_fieldset']['order_id'])
            );

            $response = $shipmentClass->createShipment();
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('Order not exist')
            );
        }



        try {
            $this->messageManager->addSuccessMessage(__('success'));
//        } catch (LocalizedException $e) {
//            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('error')
            );
        }

        return $this->resultRedirectFactory->create()->setPath('sales/order/view', ['order_id' => 1]);
    }
}
