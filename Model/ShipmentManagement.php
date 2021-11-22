<?php

namespace Smartmage\Inpost\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
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
    private $shipmentFactory;

    /**
     * @var Api\ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Core store config
     *
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var DateTimeFactory
     */
    private $dateFactory;

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
        DateTimeFactory $dateFactory
    ) {
        $this->shipmentFactory = $shipmentFactory;
        $this->shipmentRepository = $shipmentRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->dateFactory = $dateFactory;
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

        /**
         * @var $shipment ShipmentInterface
         */
        if ($count === 0) {
            $shipment = $this->shipmentFactory->create();
            $shipment->setShipmentId($shipmentData[ShipmentInterface::SHIPMENT_ID]);
        } elseif ($count === 1) {
            $shipment = $obtainedShipments->getItems()[0];
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
        $this->shipmentRepository->save($shipment);
    }

}
