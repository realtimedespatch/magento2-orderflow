<?php

namespace RealtimeDespatch\OrderFlow\Api;

/**
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
