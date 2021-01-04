<?php

namespace RealtimeDespatch\OrderFlow\Plugin\Catalog;

use Magento\Catalog\Model\Product;
use RealtimeDespatch\OrderFlow\Model\Source\Export\Status as ExportStatus;

/**
 * Class ProductSave
 *
 * Resets the export status if the product has been updated.
 */
class ProductSave
{
    const EXPORT_STATUS_KEY = 'orderflow_export_status';

    /**
     * Resets the export status for a product that has been updated.
     *
     * This ensures the product is queued for export into OrderFlow.
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product $resourceModel
     * @param Product $product
     * @return Product[]
     * @SuppressWarnings("unused")
     * @noinspection PhpUnusedParameterInspection
     */
    public function beforeSave(
        \Magento\Catalog\Model\ResourceModel\Product $resourceModel,
        Product $product
    ) {
        // Product has no changes.
        if (! $product->isDataChanged()) {
            return [$product];
        }

        // The export status as already been updated.
        if ($product->dataHasChangedFor(self::EXPORT_STATUS_KEY)) {
            return [$product];
        }

        // Set export status as pending to trigger the push to OrderFlow
        $product->setData(self::EXPORT_STATUS_KEY, ExportStatus::STATUS_PENDING);

        return [$product];
    }
}
