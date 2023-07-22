<?php

namespace Smartmage\Inpost\Controller\Adminhtml\Shipments;

use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Magento\Backend\App\Action;
use Magento\Sales\Api\OrderRepositoryInterface;
use Smartmage\Inpost\Model\ApiShipx\CallResult;
use Smartmage\Inpost\Api\Data\ShipmentOrderLinkInterfaceFactory;
use Smartmage\Inpost\Api\ShipmentOrderLinkRepositoryInterface;
use Smartmage\Inpost\Model\ApiShipx\Service\Shipment\Create\Courier;
use Smartmage\Inpost\Model\ApiShipx\Service\Shipment\Create\Locker;

class Save extends AbstractSave
{
    /**
     * @var ShipmentOrderLinkInterfaceFactory
     */
    private $orderLinkFactory;

    /**
     * @var ShipmentOrderLinkRepositoryInterface
     */
    private $orderLinkRepository;

    /**
     * @var PsrLoggerInterface
     */
    protected $logger;

    /**
     * Save constructor.
     * @param Action\Context $context
     * @param Courier $courier
     * @param Locker $locker
     * @param OrderRepositoryInterface $orderRepository
     * @param ShipmentOrderLinkInterfaceFactory $orderLinkFactory
     * @param ShipmentOrderLinkRepositoryInterface $orderLinkRepository
     */
    public function __construct(
        Action\Context $context,
        Courier $courier,
        Locker $locker,
        OrderRepositoryInterface $orderRepository,
        ShipmentOrderLinkInterfaceFactory $orderLinkFactory,
        ShipmentOrderLinkRepositoryInterface $orderLinkRepository,
        PsrLoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->orderLinkFactory = $orderLinkFactory;
        $this->orderLinkRepository = $orderLinkRepository;
        parent::__construct($context, $courier, $locker, $orderRepository);
    }

    protected function processShippment()
    {
        $data = $this->getRequest()->getParams();
        $shipmentClass = $this->classMapper[$data['shipment_fieldset']['service']];
        $order = $this->orderRepository->get($data['shipment_fieldset']['order_id']);
        $shipmentClass->createBody(
            $data['shipment_fieldset'],
            $order
        );

        $response = $shipmentClass->createShipment();

        $this->logger->info(print_r($response, true));

        if (isset($response[CallResult::STRING_RESPONSE_SHIPMENT_ID])) {
            $orderLink = $this->orderLinkFactory->create();
            $orderLink->setIncrementId($order->getIncrementId());
            $orderLink->setShipmentId($response[CallResult::STRING_RESPONSE_SHIPMENT_ID]);
            $this->orderLinkRepository->save($orderLink);
        }

        return $response;
    }
}
