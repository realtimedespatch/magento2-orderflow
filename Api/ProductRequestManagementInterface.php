<?php

namespace RealtimeDespatch\OrderFlow\Api;

/**
 * Product Request Management Interface.
 *
 * @api
 */
interface ProductRequestManagementInterface
{
    /**
     * Marks a product as exported.
     *
     * @api
     * @param string $reference
     *
     * @return mixed
     */
    public function export($reference);
}