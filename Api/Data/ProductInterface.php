<?php

namespace RealtimeDespatch\OrderFlow\Api\Data;

/**
 * Product export data interface.
 *
 * @api
 */
interface ProductInterface extends \Magento\Catalog\Api\Data\ProductInterface
{
    /**
     * Get attribute set name.
     *
     * @return string|null
     */
    public function getAttributeSetName();

    /**
     * Set attribute set name.
     *
     * @param string $attributeSetName
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ProductInterface
     */
    public function setAttributeSetName($attributeSetName);
}
