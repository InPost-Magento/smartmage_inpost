<?php

namespace Smartmage\Inpost\Model\ApiShipx\Service\Shipment;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Smartmage\Inpost\Api\Data\ShipmentOrderLinkInterfaceFactory;
use Smartmage\Inpost\Api\ShipmentOrderLinkRepositoryInterface;
use Smartmage\Inpost\Model\ApiShipx\CallResult;
use Smartmage\Inpost\Model\ApiShipx\Service\Shipment\Create\Courier;
use Smartmage\Inpost\Model\ApiShipx\Service\Shipment\Create\Locker;
use Smartmage\Inpost\Model\Config\Source\ShippingMethods;
use Smartmage\Inpost\Model\ConfigProvider;
use Smartmage\Inpost\Model\Order\Processor as OrderProcessor;

class MassCreate
{
    const LOCKER_SERVICE = 'locker';
    const COURIER_SERVICE = 'service';

    /**
     * @var ShippingMethods
     */
    protected $shippingMethods;

    /**
     * @var Locker
     */
    protected $locker;

    /**
     * @var Courier
     */
    protected $courier;

    /**
     * @var ConfigProvider
     */
    protected $configProvider;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var OrderProcessor
     */
    protected $orderProcessor;

    /**
     * @var ShipmentOrderLinkInterfaceFactory
     */
    private $orderLinkFactory;

    /**
     * @var ShipmentOrderLinkRepositoryInterface
     */
    private $orderLinkRepository;

    /**
     * MassCreate constructor.
     * @param ShippingMethods $shippingMethods
     * @param Locker $locker
     * @param Courier $courier
     * @param ConfigProvider $configProvider
     * @param PriceCurrencyInterface $priceCurrency
     * @param OrderProcessor $orderProcessor
     * @param ShipmentOrderLinkInterfaceFactory $orderLinkFactory
     * @param ShipmentOrderLinkRepositoryInterface $orderLinkRepository
     */
    public function __construct(
        ShippingMethods $shippingMethods,
        Locker $locker,
        Courier $courier,
        ConfigProvider $configProvider,
        PriceCurrencyInterface $priceCurrency,
        OrderProcessor $orderProcessor,
        ShipmentOrderLinkInterfaceFactory $orderLinkFactory,
        ShipmentOrderLinkRepositoryInterface $orderLinkRepository
    ) {
        $this->shippingMethods = $shippingMethods;
        $this->locker = $locker;
        $this->courier = $courier;
        $this->configProvider = $configProvider;
        $this->priceCurrency = $priceCurrency;
        $this->orderProcessor = $orderProcessor;
        $this->orderLinkFactory = $orderLinkFactory;
        $this->orderLinkRepository = $orderLinkRepository;
    }

    /**
     * @param $orders
     * @return array
     */
    public function createShipments($orders)
    {
        $notInpostMethodsMsg = __(
            'The following orders do not have InPost shipment created due to different delivery methods:'
        );
        $notInpostMethodsMsg .= ' ';

        $successMsg = __('InPost shipments were generated for orders:') . ' ';

        $errorMsg = [];

        $notInpostMethods = [];
        $successOrderIds = [];
        $shipmentIds = [];
        foreach ($orders as $order) {
            $this->orderProcessor->setOrder($order);
            $service = $this->shippingMethods->getInpostMethod($order->getShippingMethod());
            if ($service) {
                $result = null;
                if (strpos($service, 'inpost_locker') !== false) {
                    $data = $this->prepareData($order, self::LOCKER_SERVICE);

                    $this->locker->createBody($data, $order);
                    try {
                        $result = $this->locker->createShipment($order);
                    } catch (\Exception $e) {
                        $errorMsg[] = __('For the order') . ' ' . $order->getIncrementId()
                            . ' '
                            . __('no Inpost shipment was generated due to:') . '<br>'
                            . $e->getMessage();
                    }
                } else {
                    $data = $this->prepareData($order, self::COURIER_SERVICE);
                    $this->courier->createBody($data, $order);
                    try {
                        $result = $this->courier->createShipment($order);
                    } catch (\Exception $e) {
                        $errorMsg[] = __('For the order') . ' ' . $order->getIncrementId()
                            . ' '
                            . __('no Inpost shipment was generated due to:') . '<br>'
                            . $e->getMessage();
                    }
                }

                if (isset($result['status']) && $result['status'] == CallResult::STATUS_SUCCESS) {
                    $successOrderIds[] = $order->getIncrementId();

                    $orderLink = $this->orderLinkFactory->create();
                    $orderLink->setIncrementId($order->getIncrementId());
                    $orderLink->setShipmentId($result[CallResult::STRING_RESPONSE_SHIPMENT_ID]);
                    $this->orderLinkRepository->save($orderLink);
                    $shipmentIds[] = $result[CallResult::STRING_RESPONSE_SHIPMENT_ID];
                }
            } else {
                $notInpostMethods[] = $order->getIncrementId();
            }
        }

        $notInpostMethodsMsg .= implode(', ', $notInpostMethods);
        $successMsg .= implode(', ', $successOrderIds);

        return [
            'notInpost' => !empty($notInpostMethods) ? $notInpostMethodsMsg : false,
            'success' => !empty($successOrderIds) ? $successMsg : false,
            'error' => !empty($errorMsg) ? $errorMsg : false,
            'shipment_ids' => $shipmentIds,
        ];
    }

    /**
     * @param $order
     * @param $serviceType
     * @return array
     */
    private function prepareData($order, $serviceType)
    {
        $data = [];
        $shippingMethod = $order->getShippingMethod();
        $codes = explode('_', $shippingMethod);
        switch ($serviceType) {
            case (self::LOCKER_SERVICE):
                $extensionAttributes = $order->getExtensionAttributes();
                $data = [
                    'size' => $this->configProvider->getDefaultSize(),
                    'target_locker' => $order->getData('inpost_locker_id'),
                ];
                break;

            case (self::COURIER_SERVICE):
                $data = [
                    'company_name' => $order->getShippingAddress()->getCompany(),
                    'first_name' => $order->getShippingAddress()->getFirstname(),
                    'last_name' => $order->getShippingAddress()->getLastname(),
                    "street" => $this->orderProcessor->getStreet(),
                    "building_number" => $this->orderProcessor->getBuildingNumber(),
                    "city" => $order->getShippingAddress()->getCity(),
                    "post_code" => $order->getShippingAddress()->getPostcode(),
                    "country_code" => "PL",
                    "length" => $this->configProvider->getDefaultLength(),
                    "width" => $this->configProvider->getDefaultWidth(),
                    "height" => $this->configProvider->getDefaultHeight(),
                    "weight" => str_replace(',', '.', $this->orderProcessor->getOrderWeight()),
                ];
                break;
        }

        $data['email'] = $order->getCustomerEmail();
        $data['service'] = $shippingMethod;
        $data['reference'] = $order->getIncrementId();
        $data['phone'] = $order->getShippingAddress()->getTelephone();

        if($this->configProvider->getAutomaticInsuranceForPackage()) {
            $insurance = $this->priceCurrency->convertAndRound($order->getGrandTotal());
        } elseif($this->configProvider->getDefaultInsuranceValue()) {
            $insurance = $this->configProvider->getDefaultInsuranceValue();
        } else {
            $insurance = '';
        }
        $data['insurance'] = $insurance;
        $data['cod'] = strpos($order->getShippingMethod(), 'cod')
            ? $this->priceCurrency->convertAndRound($order->getGrandTotal())
            : '';
        $data['sending_method'] = $this->configProvider->getConfigData(
            $codes[0] . '/' . $codes[1] . '/default_way_sending'
        );

        return $data;
    }
}
