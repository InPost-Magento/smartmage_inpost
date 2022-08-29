<?php

namespace Smartmage\Inpost\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Smartmage\Inpost\Api\Data\ShipmentInterface;
use Smartmage\Inpost\Api\Data\ShipmentOrderLinkInterface;
use Smartmage\Inpost\Api\Data\ShipmentOrderLinkSearchResultsInterface;

interface ShipmentOrderLinkRepositoryInterface
{
    /**
     * Save shipment.
     *
     * @param ShipmentOrderLinkInterface|AbstractModel $shipmentOrderLink
     * @return ShipmentInterface
     * @throws Exception\CouldNotSaveException
     */
    public function save(Data\ShipmentOrderLinkInterface $shipmentOrderLink);

    /**
     * Retrieve shipment.
     *
     * @param int $linkId
     * @return ShipmentOrderLinkInterface
     * @throws LocalizedException
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
     * @param ShipmentOrderLinkInterface|AbstractModel $shipment
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(Data\ShipmentOrderLinkInterface $shipment);

    /**
     * Delete shipment by Id.
     *
     * @param int $linkId
     * @return bool true on success
     * @throws LocalizedException
     */
    public function deleteById($linkId);
}
