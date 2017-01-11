<?php

namespace RealtimeDespatch\OrderFlow\Model\Service\Request\Type;

use \RealtimeDespatch\OrderFlow\Api\RequestProcessorTypeInterface;

class ImportRequestProcessorType implements RequestProcessorTypeInterface
{
    /**
     * @var \RealtimeDespatch\OrderFlow\Model\Service\Import\Importer
     */
    protected $_importer;

    /**
     * @param \RealtimeDespatch\OrderFlow\Model\Service\Import\Importer $importer
     */
    public function __construct(\RealtimeDespatch\OrderFlow\Model\Service\Import\Importer $importer)
    {
        $this->_importer = $importer;
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
        return $this->_importer->import($request);
    }
}