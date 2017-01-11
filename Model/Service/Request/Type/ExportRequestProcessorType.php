<?php

namespace RealtimeDespatch\OrderFlow\Model\Service\Request\Type;

use \RealtimeDespatch\OrderFlow\Api\RequestProcessorTypeInterface;

class ExportRequestProcessorType implements RequestProcessorTypeInterface
{
    /**
     * @var \RealtimeDespatch\OrderFlow\Model\Service\Export\Exporter
     */
    protected $_exporter;

    /**
     * @param \RealtimeDespatch\OrderFlow\Model\Service\Export\Exporter $exporter
     */
    public function __construct(\RealtimeDespatch\OrderFlow\Model\Service\Export\Exporter $exporter)
    {
        $this->_exporter = $exporter;
    }

    /**
     * Processes an orderflow request.
     *
     * @param \RealtimeDespatch\OrderFlow\Model\Request $request
     *
     * @return boolean
     */
    public function process(\RealtimeDespatch\OrderFlow\Model\Request $request)
    {
        return $this->_exporter->export($request);
    }
}