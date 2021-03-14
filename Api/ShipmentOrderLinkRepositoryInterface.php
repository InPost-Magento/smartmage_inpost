<?php

namespace Smartmage\Inpost\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception;
use Smartmage\Inpost\Api\Data\ShipmentOrderLinkSearchResultsInterface;

interface ShipmentOrderLinkRepositoryInterface
{
    /**
     * Save shipment.
     *
     * @param \Smartmage\Inpost\Api\Data\ShipmentOrderLinkInterface|\Magento\Framework\Model\AbstractModel $shipmentOrderLink
     * @return \Smartmage\Inpost\Api\Data\ShipmentInterface
     * @throws Exception\CouldNotSaveException
     */
    public function save(Data\ShipmentOrderLinkInterface $shipmentOrderLink);

    /**
     * Retrieve shipment.
     *
     * @param int $linkId
     * @return \Smartmage\Inpost\Api\Data\ShipmentOrderLinkInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($linkId);

    /**
     * Retrieve shipment order links matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return ShipmentOrderLinkSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete shipment.
     *
     * @param \Smartmage\Inpost\Api\Data\ShipmentOrderLinkInterface|\Magento\Framework\Model\AbstractModel $shipment
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\ShipmentOrderLinkInterface $shipment);

    /**
     * Delete shipment by Id.
     *
     * @param int $linkId
     * @return bool true on success
     * @throws Exception\LocalizedException
     */
    public function deleteById($linkId);
}
