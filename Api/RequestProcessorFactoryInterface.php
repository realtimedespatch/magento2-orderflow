<?php

namespace RealtimeDespatch\OrderFlow\Api;

/**
 * Request Processor Factory Interface.
 *
 * Interface Class for the Request Processor Factory Implementation.
 */
interface RequestProcessorFactoryInterface
{
    /**
     * Returns the correct request process for specific entity, and operation types.
     *
     * @param string $entity
     * @param string $operation
     * @return mixed
     */
    public function get(string $entity, string $operation);
}
