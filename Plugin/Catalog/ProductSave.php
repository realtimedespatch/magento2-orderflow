<?php

namespace RealtimeDespatch\OrderFlow\Plugin\Catalog;

/**
 * Class ProductSave
 * @package RealtimeDespatch\OrderFlow\Plugin\Catalog
 */
class ProductSave
{
    const EXPORT_STATUS_PENDING = 'Pending';

    /**
     * Resets the Export Status if a product has been updated.
     *
     * @param \Magento\Catalog\Model\Product $product
     */
    public function beforeSave($resourceModel, $product)
    {
        // If the product has no changed data ignore.
        if ( ! $product->isDataChanged()) {
            return array($product);
        }

        // If a separate process has amended the export status ignore.
        if ($product->dataHasChangedFor('orderflow_export_status')) {
            return array($product);
        }

        // Set pending export status.
        $product->setOrderflowExportStatus(self::EXPORT_STATUS_PENDING);

        return array($product);
    }
}