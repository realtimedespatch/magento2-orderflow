<?php

namespace RealtimeDespatch\OrderFlow\Model\Service\Request\Type;

use \RealtimeDespatch\OrderFlow\Api\RequestProcessorTypeInterface;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use RealtimeDespatch\OrderFlow\Model\Service\Import\Importer;

/**
 * Import Request Processor.
 *
 * Processes an import request - see types under the RealtimeDespatch\OrderFlow\Model\Service\Import\Type namespace
 */
class ImportRequestProcessorType implements RequestProcessorTypeInterface
{
    /**
     * @var Importer
     */
    protected $importer;

    /**
     * @param Importer $importer
     */
    public function __construct(Importer $importer)
    {
        $this->importer = $importer;
    }

    /**
     * @inheritDoc
     */
    public function process(RequestInterface $request)
    {
        return $this->importer->import($request);
    }
}
