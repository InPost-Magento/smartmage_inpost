<?php

namespace Smartmage\Inpost\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception;
use Smartmage\Inpost\Api\Data\ShipmentSearchResultsInterface;

interface ShipmentRepositoryInterface
{
    /**
     * Save shipment.
     *
     * @param \Smartmage\Inpost\Api\Data\ShipmentInterface|\Magento\Framework\Model\AbstractModel $shipment
     * @return \Smartmage\Inpost\Api\Data\ShipmentInterface
     * @throws Exception\CouldNotSaveException
     */
    public function save(Data\ShipmentInterface $shipment);

    /**
     * Retrieve shipment.
     *
     * @param int $shipmentId
     * @return \Smartmage\Inpost\Api\Data\ShipmentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($shipmentId);

    /**
     * Retrieve shipments matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return ShipmentSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete shipment.
     *
     * @param \Smartmage\Inpost\Api\Data\ShipmentInterface|\Magento\Framework\Model\AbstractModel $shipment
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\ShipmentInterface $shipment);

    /**
     * Delete shipment by Id.
     *
     * @param int $shipmentId
     * @return bool true on success
     * @throws Exception\LocalizedException
     */
    public function deleteById($shipmentId);
}
