<?php

namespace RealtimeDespatch\OrderFlow\Api;

/**
 * Product export repository interface.
 *
 * @api
 */
interface ProductRepositoryInterface
{
    /**
     * Get info about product by SKU.
     *
     * @param string $sku
     * @param int|null $storeId
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ProductInterface
     */
    public function get($sku, $storeId = null);
}
