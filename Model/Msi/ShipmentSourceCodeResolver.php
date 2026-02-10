<?php

declare(strict_types=1);

namespace Smartmage\Inpost\Model\Msi;

use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\GetSourcesAssignedToStockOrderedByPriorityInterface;
use Magento\InventoryCatalogApi\Api\DefaultSourceProviderInterface;
use Magento\InventorySalesApi\Model\StockByWebsiteIdResolverInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Psr\Log\LoggerInterface;

class ShipmentSourceCodeResolver
{
    private StockByWebsiteIdResolverInterface $stockByWebsiteIdResolver;

    private GetSourcesAssignedToStockOrderedByPriorityInterface $getSourcesAssignedToStockOrderedByPriority;

    private DefaultSourceProviderInterface $defaultSourceProvider;

    private LoggerInterface $logger;

    public function __construct(
        StockByWebsiteIdResolverInterface $stockByWebsiteIdResolver,
        GetSourcesAssignedToStockOrderedByPriorityInterface $getSourcesAssignedToStockOrderedByPriority,
        DefaultSourceProviderInterface $defaultSourceProvider,
        LoggerInterface $logger
    ) {
        $this->stockByWebsiteIdResolver = $stockByWebsiteIdResolver;
        $this->getSourcesAssignedToStockOrderedByPriority = $getSourcesAssignedToStockOrderedByPriority;
        $this->defaultSourceProvider = $defaultSourceProvider;
        $this->logger = $logger;
    }

    /**
     * Resolve MSI source code for shipment based on order website/stock configuration.
     *
     */
    public function resolveForOrder(OrderInterface $order): ?string
    {
        try {
            $store = $order->getStore();
            if ($store === null) {
                return null;
            }

            $websiteId = (int) $store->getWebsiteId();
            if ($websiteId <= 0) {
                return null;
            }

            $stock = $this->stockByWebsiteIdResolver->execute($websiteId);
            $stockId = (int) $stock->getStockId();
            if ($stockId <= 0) {
                return null;
            }

            $sources = $this->getSourcesAssignedToStockOrderedByPriority->execute($stockId);

            /** @var SourceInterface[] $sources */
            $enabledSources = [];
            foreach ($sources as $source) {
                if ($source->isEnabled()) {
                    $enabledSources[] = $source;
                }
            }

            if (!empty($enabledSources)) {
                $source = reset($enabledSources);
                $sourceCode = $source instanceof SourceInterface ? $source->getSourceCode() : null;
            } elseif (!empty($sources)) {
                $source = reset($sources);
                $sourceCode = $source instanceof SourceInterface ? $source->getSourceCode() : null;
            } else {
                $sourceCode = $this->defaultSourceProvider->getCode();
            }

            if (is_string($sourceCode) && $sourceCode !== '') {
                return $sourceCode;
            }

            return null;
        } catch (\Throwable $e) {
            $this->logger->error(
                sprintf(
                    'Smartmage_Inpost: Cannot resolve MSI source code for order %s: %s',
                    $order->getIncrementId(),
                    $e->getMessage()
                )
            );

            return null;
        }
    }
}

