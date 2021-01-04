<?php

namespace RealtimeDespatch\OrderFlow\Api\Data;

/**
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
     * @param $sku string The sku
     * @return QuantityItemInterface
     * @api
     */
    public function setSku(string $sku);

    /**
     * Set the quantity
     *
     * @param $qty string
     * @return QuantityItemInterface
     * @api
     */
    public function setQty(string $qty);
}
