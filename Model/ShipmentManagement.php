<?php

namespace Smartmage\Inpost\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Stdlib\DateTime\DateTimeFactory;
use Magento\Store\Model\StoreManagerInterface;
use Smartmage\Inpost\Api;
use Smartmage\Inpost\Api\Data\ShipmentInterface;

/**
 * Class ShipmentManagement
 * @package Smartmage\Inpost\Model
 */
class ShipmentManagement implements Api\ShipmentManagementInterface
{

    /**
     * @var Api\Data\ShipmentInterfaceFactory
     */
    private Api\Data\ShipmentInterfaceFactory $shipmentFactory;

    /**
     * @var Api\ShipmentRepositoryInterface
     */
    private Api\ShipmentRepositoryInterface $shipmentRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var SortOrderBuilder
     */
    private SortOrderBuilder $sortOrderBuilder;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * Core store config
     *
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @var DateTimeFactory
     */
    private DateTimeFactory $dateFactory;

    /**
     * @var EventManager
     */
    private EventManager $eventManager;

    /**
     * ShipmentManagement constructor.
     *
     * @param Api\Data\ShipmentInterfaceFactory $shipmentFactory
     * @param Api\ShipmentRepositoryInterface $shipmentRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param DateTimeFactory $dateFactory
     */
    public function __construct(
        Api\Data\ShipmentInterfaceFactory $shipmentFactory,
        Api\ShipmentRepositoryInterface $shipmentRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        DateTimeFactory $dateFactory,
        EventManager $eventManager
    ) {
        $this->shipmentFactory = $shipmentFactory;
        $this->shipmentRepository = $shipmentRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->dateFactory = $dateFactory;
        $this->eventManager = $eventManager;
    }

    /**
     * @inheritdoc
     */
    public function addOrUpdate($shipmentData)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(ShipmentInterface::SHIPMENT_ID, $shipmentData[ShipmentInterface::SHIPMENT_ID])
            ->create();

        $obtainedShipments = $this->shipmentRepository->getList($searchCriteria);
        $count = $obtainedShipments->getTotalCount();
        $trackingNumberExisted = false;

        /**
         * @var $shipment ShipmentInterface
         */
        if ($count === 0) {
            $shipment = $this->shipmentFactory->create();
            $shipment->setShipmentId($shipmentData[ShipmentInterface::SHIPMENT_ID]);
        } elseif ($count === 1) {
            $shipment = $obtainedShipments->getItems()[0];
            $trackingNumberExisted = ($shipment->getTrackingNumber());
        }
        $shipment->setStatus($shipmentData[ShipmentInterface::STATUS]);
        $shipment->setService($shipmentData[ShipmentInterface::SERVICE]);
        $shipment->setShipmentAttributes($shipmentData[ShipmentInterface::SHIPMENT_ATTRIBUTES]);
        $shipment->setSendingMethod($shipmentData[ShipmentInterface::SENDING_METHOD]);
        $shipment->setReceiverData($shipmentData[ShipmentInterface::RECEIVER_DATA]);
        $shipment->setReference($shipmentData[ShipmentInterface::REFERENCE]);
        $shipment->setTrackingNumber($shipmentData[ShipmentInterface::TRACKING_NUMBER]);
        $shipment->setTargetPoint(
            isset($shipmentData[ShipmentInterface::TARGET_POINT])
                ? $shipmentData[ShipmentInterface::TARGET_POINT]
                : ''
        );
        $shipment->setDispatchOrderId(
            isset($shipmentData[ShipmentInterface::DISPATCH_ORDER_ID])
                ? $shipmentData[ShipmentInterface::DISPATCH_ORDER_ID]
                : ''
        );
        if(!$trackingNumberExisted && $shipmentData[ShipmentInterface::TRACKING_NUMBER]) {
            $this->eventManager->dispatch('inpost_trackingnumber_received', ['inpostShipment' => $shipment]);
        }
        $this->shipmentRepository->save($shipment);
    }

}
