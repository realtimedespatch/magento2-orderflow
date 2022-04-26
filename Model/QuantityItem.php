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
     * Inventory source
     *
     * @var string
     */
    public $source;

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
     * @api
     * @param string $sku Product SKU
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\QuantityItemInterface
     */
    public function setSku($sku)
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * Set the quantity
     *
     * @api
     * @param integer $qty Stock Quantity
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\QuantityItemInterface
     */
    public function setQty($qty)
    {
        $this->qty = $qty;

        return $this;
    }

    /**
     * Get the inventory source
     *
     * @api
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set the inventory source
     *
     * @api
     * @param $source
     * @return mixed|void
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }
}
