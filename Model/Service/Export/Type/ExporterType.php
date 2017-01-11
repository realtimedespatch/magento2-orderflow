<?php

namespace RealtimeDespatch\OrderFlow\Model\Service\Export\Type;

use RealtimeDespatch\OrderFlow\Api\ExporterTypeInterface;
use RealtimeDespatch\OrderFlow\Api\Data\ExportLineInterface;

abstract class ExporterType implements ExporterTypeInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_config;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $cnfig
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\ObjectManagerInterface
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\ObjectManagerInterface $objectManager) {
        $this->_config = $config;
        $this->_logger = $logger;
        $this->_objectManager = $objectManager;
    }

    /**
     * Creates a new export.
     *
     * @param \RealtimeDespatch\OrderFlow\Model\Request request
     *
     * @return \RealtimeDespatch\OrderFlow\Model\Export
     */
    protected function _createExport(\RealtimeDespatch\OrderFlow\Model\Request $request)
    {
        $export = $this->_objectManager->create('RealtimeDespatch\OrderFlow\Model\Export');

        $export->setRequestId($request->getId());
        $export->setMessageId($request->getMessageId());
        $export->setScopeId($request->getScopeId());
        $export->setEntity($request->getEntity());
        $export->setOperation($request->getOperation());
        $export->setSuccesses(0);
        $export->setDuplicates(0);
        $export->setFailures(0);

        return $export;
    }

    /**
     * Creates a success export line.
     *
     * @param $export
     * @param $reference
     * @param $message
     * @param $data
     *
     * @return \RealtimeDespatch\OrderFlow\Model\ExportLine
     */
    protected function _createSuccessExportLine($export, $reference, $operation, $message, $data = array())
    {
        $export->setSuccesses($export->getSuccesses() + 1);

        return $this->_createExportLine(
            ExportLineInterface::RESULT_SUCCESS,
            $reference,
            $operation,
            $message,
            $data
        );
    }

    /**
     * Creates a failure export line.
     *
     * @param $export
     * @param $reference
     * @param $message
     * @param $data
     *
     * @return \RealtimeDespatch\OrderFlow\Model\ExportLine
     */
    protected function _createFailureExportLine($export, $reference, $operation, $message, $data = array())
    {
        $export->setFailures($export->getFailures() + 1);

        return $this->_createExportLine(
            ExportLineInterface::RESULT_FAILURE,
            $reference,
            $operation,
            $message,
            $data
        );
    }

    /**
     * Creates an export line.
     *
     * @param $result
     * @param $reference
     * @param $operation
     * @param $message
     * @param array $data
     *
     * @return \RealtimeDespatch\OrderFlow\Model\ExportLine
     */
    protected function _createExportLine($result, $reference, $operation, $message, $data = array())
    {
        $exportLine = $this->_objectManager->create('RealtimeDespatch\OrderFlow\Model\ExportLine');
        $exportLine->setResult($result);
        $exportLine->setReference($reference);
        $exportLine->setOperation($operation);
        $exportLine->setEntity($this->getType());
        $exportLine->setMessage($message);
        $exportLine->setDetail($message);
        $exportLine->setAdditionalData(json_encode($data));
        $exportLine->setProcessedAt(date('Y-m-d H:i:s'));

        return $exportLine;
    }
}