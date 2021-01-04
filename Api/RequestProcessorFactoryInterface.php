<?php

namespace RealtimeDespatch\OrderFlow\Api;

use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;

/**
 * Request Processor Factory Interface.
 *
 * Interface Class for the Request Processor Factory Implementation.
 */
interface RequestProcessorFactoryInterface
{
    /**
     * Request Factory Getter.
     *
     * @param RequestInterface $request
     * @return mixed
     */
    public function get(RequestInterface $request);
}
