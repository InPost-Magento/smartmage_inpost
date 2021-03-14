<?php

namespace Smartmage\Inpost\Model;

use Smartmage\Inpost\Api\Data;
use Smartmage\Inpost\Api\Data\ShipmentOrderLinkInterfaceFactory;
use Smartmage\Inpost\Api\Data\ShipmentOrderLinkSearchResultsInterface;
use Smartmage\Inpost\Api\Data\ShipmentOrderLinkSearchResultsInterfaceFactory;
use Smartmage\Inpost\Api\ShipmentOrderLinkRepositoryInterface;
use Smartmage\Inpost\Model\ResourceModel\ShipmentOrderLink;
use Smartmage\Inpost\Model\ResourceModel\ShipmentOrderLink\CollectionFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception;
use Magento\Framework\Api\SortOrder;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class ShipmentOrderLinkRepository
 * @package Smartmage\Inpost\Model
 */
class ShipmentOrderLinkRepository implements ShipmentOrderLinkRepositoryInterface
{
    /**
     * @var Shipment
     */
    private $resource;

    /**
     * @var ShipmentOrderLinkInterfaceFactory
     */
    private $shipmentFactory;

    /**
     * @var ShipmentOrderLinkSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var CollectionFactory
     */
    private $shipmentCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;


    /**
     * ShipmentOrderLinkRepository constructor.
     * @param \Smartmage\Inpost\Model\ResourceModel\ShipmentOrderLink $resource
     * @param \Smartmage\Inpost\Api\Data\ShipmentOrderLinkInterfaceFactory $shipmentFactory
     * @param \Smartmage\Inpost\Api\Data\ShipmentOrderLinkSearchResultsInterfaceFactory $searchResultsFactory
     * @param \Smartmage\Inpost\Model\ResourceModel\ShipmentOrderLink\CollectionFactory $shipmentCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        ShipmentOrderLink $resource,
        ShipmentOrderLinkInterfaceFactory $shipmentFactory,
        ShipmentOrderLinkSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionFactory $shipmentCollectionFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->shipmentFactory = $shipmentFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->shipmentCollectionFactory = $shipmentCollectionFactory;
        $this->storeManager = $storeManager;
    }


    /**
     * @param \Smartmage\Inpost\Api\Data\ShipmentOrderLinkInterface $shipment
     * @return \Smartmage\Inpost\Api\Data\ShipmentOrderLinkInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(Data\ShipmentOrderLinkInterface $shipment)
    {
        try {
            $this->resource->save($shipment);
        } catch (\Exception $e) {
            throw new Exception\CouldNotSaveException(__($e->getMessage()));
        }
        return $shipment;
    }


    /**
     * @param int $entityId
     * @return \Magento\Framework\Model\AbstractModel|\Smartmage\Inpost\Api\Data\ShipmentOrderLinkInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($entityId)
    {
        /** @var \Smartmage\Inpost\Api\Data\ShipmentOrderLinkInterface|\Magento\Framework\Model\AbstractModel $shipment */
        $shipment = $this->shipmentFactory->create();
        $this->resource->load($shipment, $entityId);
        if (!$shipment->getId()) {
            throw new Exception\NoSuchEntityException(__('Shipment with id "%1" does not exist.', $entityId));
        }
        return $shipment;
    }

    /**
     * @param $shipmentId
     * @return \Magento\Framework\Model\AbstractModel|\Smartmage\Inpost\Api\Data\ShipmentOrderLinkInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByShipmentId($shipmentId)
    {
        /** @var \Smartmage\Inpost\Api\Data\ShipmentOrderLinkInterface|\Magento\Framework\Model\AbstractModel $shipment */
        $shipment = $this->shipmentFactory->create();
        $this->resource->load($shipment, $shipmentId, 'shipment_id');
        if (!$shipment->getId($shipment)) {
            throw new Exception\NoSuchEntityException(__('Shipment with shipment_id "%1" does not exist.', $shipmentId));
        }
        return $shipment;
    }


    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Smartmage\Inpost\Api\Data\ShipmentOrderLinkSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        $collection = $this->shipmentCollectionFactory->create();
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        $shipments = [];
        foreach ($collection as $shipment) {
            $shipments[] = $shipment;
        }
        $searchResults->setItems($shipments);
        return $searchResults;
    }


    /**
     * @param \Smartmage\Inpost\Api\Data\ShipmentOrderLinkInterface $shipment
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(Data\ShipmentOrderLinkInterface $shipment)
    {
        try {
            $this->resource->delete($shipment);
        } catch (\Exception $e) {
            throw new Exception\CouldNotDeleteException(__($e->getMessage()));
        }
        return true;
    }

    /**
     * @param int $entityId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById($entityId)
    {
        return $this->delete($this->getById($entityId));
    }
}
