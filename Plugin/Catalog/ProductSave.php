<?php

namespace RealtimeDespatch\OrderFlow\Plugin\Catalog;

use RealtimeDespatch\OrderFlow\Model\Product\ExportStatus\ProductExportStatusResolver;

/**
 * Class ProductSave
 * @package RealtimeDespatch\OrderFlow\Plugin\Catalog
 */
class ProductSave
{
    public function __construct(
        private readonly ProductExportStatusResolver $productExportStatusResolver
    ) {}

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

        if (!$this->productExportStatusResolver->shouldSetPending(
            $product->getData('orderflow_export_status'),
            $product->dataHasChangedFor('orderflow_export_status')
        )) {
            return array($product);
        }

        // Set pending export status.
        $product->setOrderflowExportStatus(ProductExportStatusResolver::STATUS_PENDING);

        return array($product);
    }
}
