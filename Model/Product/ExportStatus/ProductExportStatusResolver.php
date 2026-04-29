<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Model\Product\ExportStatus;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class ProductExportStatusResolver
{
    public const STATUS_PENDING = 'Pending';
    public const STATUS_DISABLED = 'Disabled';

    public function __construct(
        private readonly CollectionFactory $productCollectionFactory
    ) {}

    public function shouldSetPending($currentStatus, bool $statusChangedExplicitly): bool
    {
        if ($statusChangedExplicitly) {
            return false;
        }

        return $currentStatus !== self::STATUS_DISABLED;
    }

    /**
     * @param mixed[] $productIds
     * @return int[]
     */
    public function getProductIdsToSetPending(array $productIds): array
    {
        $productIds = $this->normalizeProductIds($productIds);
        if ($productIds === []) {
            return [];
        }

        $collection = $this->productCollectionFactory->create();
        $collection->addIdFilter($productIds);
        $collection->addAttributeToSelect('orderflow_export_status');

        return $this->extractProductIdsToSetPending($collection);
    }

    /**
     * @param mixed[] $skus
     * @return int[]
     */
    public function getProductIdsBySkusToSetPending(array $skus): array
    {
        $skus = $this->normalizeSkus($skus);
        if ($skus === []) {
            return [];
        }

        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('orderflow_export_status');
        $collection->addAttributeToFilter('sku', ['in' => $skus]);

        return $this->extractProductIdsToSetPending($collection);
    }

    /**
     * @param mixed[] $skus
     * @return string[]
     */
    public function getSkusToSetPending(array $skus): array
    {
        $skus = $this->normalizeSkus($skus);
        if ($skus === []) {
            return [];
        }

        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('orderflow_export_status');
        $collection->addAttributeToFilter('sku', ['in' => $skus]);

        $eligibleSkus = array_fill_keys($skus, true);

        foreach ($collection as $product) {
            if (!$this->shouldSetPending($product->getData('orderflow_export_status'), false)) {
                $sku = trim((string)$product->getSku());
                if ($sku !== '') {
                    unset($eligibleSkus[$sku]);
                }
            }
        }

        return array_values(array_keys($eligibleSkus));
    }

    /**
     * @param mixed[] $productIds
     * @return int[]
     */
    private function normalizeProductIds(array $productIds): array
    {
        $normalizedProductIds = [];

        foreach ($productIds as $productId) {
            $productId = (int)$productId;
            if ($productId > 0) {
                $normalizedProductIds[$productId] = $productId;
            }
        }

        return array_values($normalizedProductIds);
    }

    /**
     * @param mixed[] $skus
     * @return string[]
     */
    private function normalizeSkus(array $skus): array
    {
        $normalizedSkus = [];

        foreach ($skus as $sku) {
            $sku = trim((string)$sku);
            if ($sku !== '') {
                $normalizedSkus[$sku] = $sku;
            }
        }

        return array_values($normalizedSkus);
    }

    /**
     * @param iterable<\Magento\Catalog\Api\Data\ProductInterface> $products
     * @return int[]
     */
    private function extractProductIdsToSetPending(iterable $products): array
    {
        $eligibleProductIds = [];

        foreach ($products as $product) {
            if (!$this->shouldSetPending($product->getData('orderflow_export_status'), false)) {
                continue;
            }

            $productId = (int)$product->getId();
            if ($productId > 0) {
                $eligibleProductIds[] = $productId;
            }
        }

        return $eligibleProductIds;
    }
}
