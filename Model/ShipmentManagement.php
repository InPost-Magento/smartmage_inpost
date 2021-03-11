<?php

namespace Smartmage\Inpost\Model;

use Smartmage\Inpost\Api;
use Smartmage\Inpost\Api\Data\ShipmentInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\DateTime\DateTimeFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;

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
    public function add($shipmentId)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(ShipmentInterface::SHIPMENT_ID, (int)$shipmentId)
            ->create();

        $count = $this->shipmentRepository->getList($searchCriteria)->getTotalCount();

        if ($count === 0) {
            $shipment = $this->shipmentFactory->create();
//            $shipment->setProductId($product->getId());
//            $shipment->setEmail($email);
            $this->shipmentRepository->save($shipment);
        }
    }

    /**
     * @inheritdoc
     */
    public function remove($shipmentId)
    {
//        $this->searchCriteriaBuilder
//            ->addFilter(ShipmentInterface::EMAIL, $email);
//        if (null !== $productId) {
//            $this->searchCriteriaBuilder
//                ->addFilter(ShipmentInterface::PRODUCT_ID, $productId);
//        }
//        $list = $this->shipmentRepository->getList($this->searchCriteriaBuilder->create());
//        foreach ($list->getItems() as $item) {
//            if ($secret === $this->getHash($email, $productId)) {
//                $this->shipmentRepository->delete($item);
//            }
//        }
    }

}
