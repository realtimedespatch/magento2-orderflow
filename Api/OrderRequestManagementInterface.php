<?php

namespace RealtimeDespatch\OrderFlow\Api;

/**
 * @api
 */
interface OrderRequestManagementInterface
{
    /**
     * Marks an order as exported.
     *
     * @param string $reference
     *
     * @return mixed
     * @api
     */
    public function export(string $reference);
}
