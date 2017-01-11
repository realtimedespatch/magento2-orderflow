<?php

namespace RealtimeDespatch\OrderFlow\Api;

/**
 * Order Request Management Interface.
 *
 * @api
 */
interface OrderRequestManagementInterface
{
    /**
     * Marks an order as exported.
     *
     * @api
     * @param string $reference
     *
     * @return mixed
     */
    public function export($reference);
}