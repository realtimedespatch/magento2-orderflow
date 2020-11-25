<?php

namespace RealtimeDespatch\OrderFlow\Plugin\Catalog;

use Magento\Catalog\Model\Product;

/**
 * Class ProductSave
 *
 * Resets the export status if the product has been updated.
 */
class ProductSave
{
    const EXPORT_STATUS_PENDING = 'Pending';

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
        // Ignore if the product has not changed, or the status has already been updated by a different listener
        if ($product->isDataChanged() || $product->dataHasChangedFor('orderflow_export_status')) {
            return [$product];
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $product->setOrderflowExportStatus(self::EXPORT_STATUS_PENDING);

        return [$product];
    }
}
