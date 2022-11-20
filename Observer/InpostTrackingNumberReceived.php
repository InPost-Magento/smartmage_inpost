<?php
namespace Smartmage\Inpost\Observer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException as LocalizedException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Sales\Api\Data\ShipmentTrackInterfaceFactory;
use Magento\Sales\Model\Convert\Order as ConvertOrder;
use Magento\Sales\Model\Order\ShipmentRepository as ShipmentRepository;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\OrderRepository;
use Magento\Shipping\Model\ShipmentNotifier as ShipmentNotifier;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Smartmage\Inpost\Api\ShipmentOrderLinksProviderInterface;
use Smartmage\Inpost\Model\Config\Source\Service as InpostService;
use Smartmage\Inpost\Model\ConfigProvider;

class InpostTrackingNumberReceived implements ObserverInterface
{
    const CONFIG_CREATE_ORDER_SHIPMENT = 'shipping/inpost/create_order_shipment';
    const CONFIG_NOTIFY_ORDER_SHIPMENT = 'shipping/inpost/notify_order_shipment';
    const CONFIG_ORDER_SHIPMENT_STATUS = 'shipping/inpost/order_shipment_status';

    protected MessageManagerInterface $messageManager;
    private OrderFactory $orderFactory;
    private OrderRepository $orderRepository;
    protected PsrLoggerInterface $logger;
    protected ScopeConfigInterface $scopeConfig;
    private ShipmentNotifier $shipmentNotifier;
    private ShipmentRepository $shipmentRepository;
    protected ShipmentOrderLinksProviderInterface $shipmentOrderLinksProvider;
    protected StoreManagerInterface $storeManager;
    private ConvertOrder $convertOrder;
    private ShipmentTrackInterfaceFactory $trackFactory;
    private InpostService $inpostService;
    protected ConfigProvider $configProvider;

    public function __construct(
        MessageManagerInterface             $messageManager,
        OrderFactory                        $orderFactory,
        PsrLoggerInterface                  $logger,
        ScopeConfigInterface                $scopeConfig,
        ShipmentNotifier                    $shipmentNotifier,
        ShipmentRepository                  $shipmentRepository,
        ShipmentOrderLinksProviderInterface $shipmentOrderLinksProvider,
        ConvertOrder                        $convertOrder,
        ShipmentTrackInterfaceFactory       $trackFactory,
        OrderRepository                     $orderRepository,
        InpostService                       $inpostService,
        ConfigProvider                      $configProvider
    ) {
        $this->logger = $logger;
        $this->messageManager = $messageManager;
        $this->orderFactory = $orderFactory;
        $this->scopeConfig = $scopeConfig;
        $this->shipmentNotifier = $shipmentNotifier;
        $this->shipmentRepository = $shipmentRepository;
        $this->shipmentOrderLinksProvider = $shipmentOrderLinksProvider;
        $this->convertOrder = $convertOrder;
        $this->trackFactory = $trackFactory;
        $this->orderRepository = $orderRepository;
        $this->inpostService = $inpostService;
        $this->configProvider = $configProvider;
    }

    public function execute(Observer $observer)
    {
        $inpostShipment = $observer->getData('inpostShipment');
        $orderIncrementId = false;
        try {
            $orderIncrementId = $this->shipmentOrderLinksProvider->getOrderIncrementId($inpostShipment->getShipmentId());
        } catch (\Exception $e) {
            $this->logger->info(print_r($e->getMessage(), true));
        }
        if ($orderIncrementId === false) {
            return false;
        }

        $order = $this->orderFactory->create()->loadByIncrementId($orderIncrementId);

        $createOrderShipment = $this->scopeConfig->getValue(
            self::CONFIG_CREATE_ORDER_SHIPMENT,
            ScopeInterface::SCOPE_STORE,
            $order->getStore()->getId()
        );

        if ($createOrderShipment) {
            $this->generateOrderShipment($order, $inpostShipment);
        }

        return $this;
    }


    public function generateOrderShipment($order, $inpostShipment): bool
    {
        try {
            if (!$order->getId()) {
                throw new LocalizedException(__('The order no longer exists.'));
            }

            $orderShipment = $order->getShipmentsCollection()->getFirstItem();
            if (!$orderShipment->getId()) {
                if (!$order->canShip()) {
                    throw new LocalizedException(
                        __('You can\'t create an shipment for order %1.', $order->getIncrementId())
                    );
                }
                $orderShipment = $this->convertOrder->toShipment($order);
                foreach ($order->getAllItems() as $orderItem) {
                    if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                        continue;
                    }
                    $qtyShipped = $orderItem->getQtyToShip();
                    $shipmentItem = $this->convertOrder->itemToShipmentItem($orderItem)->setQty($qtyShipped);
                    $orderShipment->addItem($shipmentItem);
                }
                $orderShipment->register();
            }

            // create track
            $carrierCode = (substr($inpostShipment->getService(), 0, 13) ==  'inpost_locker') ? 'inpostlocker' : 'inpostcourier';
            $trackTitle = $this->getShippingMethodTitle($inpostShipment->getShippingMethod());
            $data = [
                'carrier_code' => $carrierCode,
                'title' => $trackTitle,
                'number' => $inpostShipment->getTrackingNumber()
            ];
            $track = $this->trackFactory->create()->addData($data);
            $orderShipment->addTrack($track);

            // support to MSI
            // if MSI has been disabled or removed then setSourceCode method does not exists
            if (method_exists($orderShipment->getExtensionAttributes(),'setSourceCode')) {
                $orderShipment->getExtensionAttributes()->setSourceCode('default');
            }


            // set order status after shipment creation 
            $orderShipmentChangeStatus = $this->scopeConfig->getValue(
                self::CONFIG_ORDER_SHIPMENT_STATUS,
                ScopeInterface::SCOPE_STORE,
                $order->getStore()->getId()
            );
            if ($orderShipmentChangeStatus) {
                $order->setStatus($orderShipmentChangeStatus);
            }

            // save order and shipment
            try {
                $this->orderRepository->save($order);
                $this->shipmentRepository->save($orderShipment);
            } catch (\Exception $e) {
                $this->logger->error($e);
                $this->messageManager->addExceptionMessage($e, $e->getMessage());
            }

            // send shipping confirmation e-mail to customer
            $notifyOrderShipment = $this->scopeConfig->getValue(
                self::CONFIG_NOTIFY_ORDER_SHIPMENT,
                ScopeInterface::SCOPE_STORE,
                $order->getStore()->getId()
            );
            if ($notifyOrderShipment) {
                $this->shipmentNotifier->notify($orderShipment);
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return true;
    }

    public function getShippingMethodTitle($shippingMethodCode)
    {
        if($shippingMethodCode) {
            $carrierMethod = explode('_',$shippingMethodCode);
            return $this->configProvider->getConfigData(
                $carrierMethod[0] . '/' . $carrierMethod[1] . '/name'
            );
        } else {
            return '';
        }
    }
}
