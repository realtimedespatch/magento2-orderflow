<?php

namespace RealtimeDespatch\OrderFlow\Api;

use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;

/**
 * Request Builder Interface.
 *
 * Defines the methods available for a request builder.
 *
 * @api
 */
interface RequestBuilderInterface
{
    /**
     * Returns a new request instance.
     *
     * @return RequestInterface
     */
    public function saveRequest();
}
