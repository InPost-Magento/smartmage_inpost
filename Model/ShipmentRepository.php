<?php

namespace Smartmage\Inpost\Model;

use Smartmage\Inpost\Api\Data;
use Smartmage\Inpost\Api\Data\ShipmentInterfaceFactory;
use Smartmage\Inpost\Api\Data\ShipmentSearchResultsInterface;
use Smartmage\Inpost\Api\Data\ShipmentSearchResultsInterfaceFactory;
use Smartmage\Inpost\Api\ShipmentRepositoryInterface;
use Smartmage\Inpost\Model\ResourceModel\Shipment;
use Smartmage\Inpost\Model\ResourceModel\Shipment\CollectionFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception;
use Magento\Framework\Api\SortOrder;
use Magento\Store\Model\StoreManagerInterface;

class ShipmentRepository implements ShipmentRepositoryInterface
{
    /**
     * @var Shipment
     */
    private $resource;

    /**
     * @var ShipmentInterfaceFactory
     */
    private $shipmentFactory;

    /**
     * @var ShipmentSearchResultsInterfaceFactory
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
     * ShipmentRepository constructor.
     *
     * @param ResourceModel\Shipment $resource
     * @param ShipmentInterfaceFactory $shipmentFactory
     * @param ShipmentSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionFactory $shipmentCollectionFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Shipment $resource,
        ShipmentInterfaceFactory $shipmentFactory,
        ShipmentSearchResultsInterfaceFactory $searchResultsFactory,
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
     * Save shipment.
     *
     * @param \Smartmage\Inpost\Api\Data\ShipmentInterface|\Magento\Framework\Model\AbstractModel $shipment
     * @return \Smartmage\Inpost\Api\Data\ShipmentInterface
     * @throws Exception\CouldNotSaveException
     */
    public function save(Data\ShipmentInterface $shipment)
    {
        try {
            $this->resource->save($shipment);
        } catch (\Exception $e) {
            throw new Exception\CouldNotSaveException(__($e->getMessage()));
        }
        return $shipment;
    }

    /**
     * Retrieve shipment.
     *
     * @param int $entityId
     * @return \Smartmage\Inpost\Api\Data\ShipmentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($entityId)
    {
        /** @var \Smartmage\Inpost\Api\Data\ShipmentInterface|\Magento\Framework\Model\AbstractModel $shipment */
        $shipment = $this->shipmentFactory->create();
        $this->resource->load($shipment, $entityId);
        if (!$shipment->getId()) {
            throw new Exception\NoSuchEntityException(__('Shipment with id "%1" does not exist.', $entityId));
        }
        return $shipment;
    }

    public function getByShipmentId($shipmentId)
    {
        /** @var \Smartmage\Inpost\Api\Data\ShipmentInterface|\Magento\Framework\Model\AbstractModel $shipment */
        $shipment = $this->shipmentFactory->create();
        $this->resource->load($shipment, $shipmentId, 'shipment_id');
        if (!$shipment->getId($shipment)) {
            throw new Exception\NoSuchEntityException(__('Shipment with shipment_id "%1" does not exist.', $shipmentId));
        }
        return $shipment;
    }

    /**
     * Retrieve shipments matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return ShipmentSearchResultsInterface
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
     * Delete shipment.
     *
     * @param \Smartmage\Inpost\Api\Data\ShipmentInterface|\Magento\Framework\Model\AbstractModel $shipment
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\ShipmentInterface $shipment)
    {
        try {
            $this->resource->delete($shipment);
        } catch (\Exception $e) {
            throw new Exception\CouldNotDeleteException(__($e->getMessage()));
        }
        return true;
    }

    /**
     * Delete shipment by entityId.
     *
     * @param int $entityId
     * @return bool true on success
     * @throws Exception\LocalizedException
     */
    public function deleteById($entityId)
    {
        return $this->delete($this->getById($entityId));
    }
}
