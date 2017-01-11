<?php

namespace RealtimeDespatch\OrderFlow\Api\Data;

/**
 * Quantity Item Interface
 *
 * @api
 */
interface QuantityItemInterface
{
    /**
     * Get the sku
     *
     * @api
     * @return string The sku
     */
    public function getSku();

    /**
     * Get the quantity
     *
     * @api
     * @return string The quantity
     */
    public function getQty();

    /**
     * Set the sku
     *
     * @api
     * @param $sku string The sku
     * @return \RealtimeDespatch\OrderFlow\Api\Data\QuantityItemInterface
     */
    public function setSku($sku);

    /**
     * Set the quantity
     *
     * @api
     * @param $qty string
     * @return \RealtimeDespatch\OrderFlow\Api\Data\QuantityItemInterface
     */
    public function setQty($qty);
}