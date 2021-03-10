<?php

namespace Smartmage\Inpost\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface ShipmentSearchResultsInterface
 *
 * @package Smartmage\ProductEmailAlert\Api\Data
 */
interface ShipmentSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get shipments list.
     *
     * @return \Smartmage\Inpost\Api\Data\ShipmentInterface[]
     */
    public function getItems();

    /**
     * Set shipments list.
     *
     * @param \Smartmage\Inpost\Api\Data\ShipmentInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
