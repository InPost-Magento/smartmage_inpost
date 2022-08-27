<?php

namespace Smartmage\Inpost\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface ShipmentSearchResultsInterface
 */
interface ShipmentOrderLinkSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get shipments list.
     *
     * @return \Smartmage\Inpost\Api\Data\ShipmentOrderLinkInterface[]
     */
    public function getItems();

    /**
     * Set shipments list.
     *
     * @param \Smartmage\Inpost\Api\Data\ShipmentOrderLinkInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
