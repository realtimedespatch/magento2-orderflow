<?php

namespace RealtimeDespatch\OrderFlow\Api;

use RealtimeDespatch\OrderFlow\Api\Data\ExportInterface;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;

/**
 * Request Processor Type Interface.
 *
 * Defines the methods available for a request type processor.
 *
 * There are two types of requests:
 *
 * 1. Imports from OrderFlow to Magento
 * 2. Exports from Magento to OrderFlow
 */
interface RequestProcessorTypeInterface
{
    /**
     * Process API Request.
     *
     * @param RequestInterface $request
     *
     * @return ExportInterface
     */
    public function process(RequestInterface $request);
}
