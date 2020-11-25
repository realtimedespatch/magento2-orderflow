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
     * @param string $reference
     *
     * @return mixed
     * @api
     */
    public function export(string $reference);
}
