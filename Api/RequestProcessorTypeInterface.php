<?php

namespace RealtimeDespatch\OrderFlow\Api;

/**
 * Request Processor Type Interface.
 */
interface RequestProcessorTypeInterface
{
    /**
     * Processes an orderflow request.
     *
     * @param \RealtimeDespatch\OrderFlow\Model\Request $request
     *
     * @return boolean
     */
    public function process(\RealtimeDespatch\OrderFlow\Model\Request $request);
}