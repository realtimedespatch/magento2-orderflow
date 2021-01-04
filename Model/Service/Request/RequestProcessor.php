<?php

namespace RealtimeDespatch\OrderFlow\Model\Service\Request;

use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use RealtimeDespatch\OrderFlow\Api\RequestProcessorTypeInterface;

/**
 * Request Processor.
 *
 * Processes an API Request according to type.
 */
class RequestProcessor
{
    /**
     * @var RequestProcessorTypeInterface
     */
    protected $type;

    /**
     * @param RequestProcessorTypeInterface $type
     */
    public function __construct(RequestProcessorTypeInterface $type)
    {
        $this->type = $type;
    }

    /**
     * Process Request.
     *
     * @param RequestInterface $request
     *
     * @return boolean
     */
    public function process(RequestInterface $request)
    {
        if (! $request->canProcess()) {
            return false;
        }

        return $this->type->process($request);
    }
}
