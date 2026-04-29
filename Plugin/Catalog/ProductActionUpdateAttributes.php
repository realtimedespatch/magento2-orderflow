<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Plugin\Catalog;

use Magento\Catalog\Model\Product\Action as ProductAction;
use Magento\Catalog\Model\ResourceModel\Product\Action as ProductActionResource;
use Magento\Store\Model\Store;
use RealtimeDespatch\OrderFlow\Model\Product\ExportStatus\ProductExportStatusResolver;

class ProductActionUpdateAttributes
{
    public function __construct(
        private readonly ProductExportStatusResolver $productExportStatusResolver,
        private readonly ProductActionResource $productActionResource
    ) {}

    public function aroundUpdateAttributes(
        ProductAction $subject,
        callable $proceed,
        $productIds,
        $attrData,
        $storeId
    ) {
        $result = $proceed($productIds, $attrData, $storeId);

        if (array_key_exists('orderflow_export_status', $attrData)) {
            return $result;
        }

        $eligibleProductIds = $this->productExportStatusResolver->getProductIdsToSetPending((array)$productIds);
        if ($eligibleProductIds === []) {
            return $result;
        }

        $this->productActionResource->updateAttributes(
            $eligibleProductIds,
            ['orderflow_export_status' => ProductExportStatusResolver::STATUS_PENDING],
            Store::DEFAULT_STORE_ID
        );

        return $result;
    }
}
