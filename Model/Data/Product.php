<?php

namespace RealtimeDespatch\OrderFlow\Model\Data;

use RealtimeDespatch\OrderFlow\Api\Data\ProductInterface;

/**
 * Product export data object.
 *
 * @api
 */
class Product extends \Magento\Catalog\Model\Product implements ProductInterface
{
    /**
     * @inheritdoc
     */
    public function getAttributeSetName()
    {
        return $this->getData('attribute_set_name');
    }

    /**
     * @inheritdoc
     */
    public function setAttributeSetName($attributeSetName)
    {
        return $this->setData('attribute_set_name', $attributeSetName);
    }
}
