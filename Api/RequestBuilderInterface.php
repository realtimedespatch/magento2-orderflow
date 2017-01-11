<?php

namespace RealtimeDespatch\OrderFlow\Api;

/**
 * Request Builder Interface.
 *
 * @api
 */
interface RequestBuilderInterface
{
    /**
     * Returns a new request instance.
     *
     * @return RealtimeDespatch\OrderFlow\Api\Data\RequestInterface
     */
    public function saveRequest();
}