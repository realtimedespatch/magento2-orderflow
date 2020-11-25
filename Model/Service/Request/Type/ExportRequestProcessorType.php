<?php

namespace RealtimeDespatch\OrderFlow\Model\Service\Request\Type;

use Magento\Framework\Exception\LocalizedException;
use RealtimeDespatch\OrderFlow\Api\RequestProcessorTypeInterface;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use RealtimeDespatch\OrderFlow\Model\Service\Export\Exporter;

/**
 * Export Request Processor.
 *
 * Processes an export request - see types under the RealtimeDespatch\OrderFlow\Model\Service\Export\Type namespace
 */
class ExportRequestProcessorType implements RequestProcessorTypeInterface
{
    /**
     * @var Exporter
     */
    protected $exporter;

    /**
     * @param Exporter $exporter
     */
    public function __construct(Exporter $exporter)
    {
        $this->exporter = $exporter;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function process(RequestInterface $request)
    {
        return $this->exporter->export($request);
    }
}
