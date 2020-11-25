<?php

namespace RealtimeDespatch\OrderFlow\Model;

use RealtimeDespatch\OrderFlow\Api\Data\QuantityItemInterface;

/**
 * Class QuantityItem
 *
 * @api
 * @package RealtimeDespatch\OrderFlow\Model
 */
class QuantityItem implements QuantityItemInterface
{
    /**
     * Product SKU
     *
     * @var string
     */
    public $sku;

    /**
     * Quantity
     *
     * @var integer
     */
    public $qty;

    /**
     * QuantityItem constructor.
     */
    public function __construct() {
        $this->sku = null;
        $this->qty = null;
    }

    /**
     * Get the sku
     *
     * @api
     * @return string The sku
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * Get the quantity
     *
     * @api
     * @return string The quantity
     */
    public function getQty()
    {
        return $this->qty;
    }

    /**
     * Set the sku
     *
     * @param string $sku Product SKU
     *
     * @return QuantityItemInterface
     * @api
     */
    public function setSku(string $sku)
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * Set the quantity
     *
     * @param string $qty Stock Quantity
     *
     * @return QuantityItemInterface
     * @api
     */
    public function setQty(string $qty)
    {
        $this->qty = $qty;

        return $this;
    }
}
