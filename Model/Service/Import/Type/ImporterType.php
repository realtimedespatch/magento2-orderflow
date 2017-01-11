<?php

namespace RealtimeDespatch\OrderFlow\Model\Service\Import\Type;

use RealtimeDespatch\OrderFlow\Api\ImporterTypeInterface;
use RealtimeDespatch\OrderFlow\Api\Data\ImportLineInterface;

abstract class ImporterType implements ImporterTypeInterface
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
     * Creates a new import.
     *
     * @param \RealtimeDespatch\OrderFlow\Model\Request request
     *
     * @return \RealtimeDespatch\OrderFlow\Model\Import
     */
    protected function _createImport(\RealtimeDespatch\OrderFlow\Model\Request $request)
    {
        $import = $this->_objectManager->create('RealtimeDespatch\OrderFlow\Model\Import');

        $import->setRequestId($request->getId());
        $import->setMessageId($request->getMessageId());
        $import->setEntity($request->getEntity());
        $import->setOperation($request->getOperation());
        $import->setSuccesses(0);
        $import->setSuperseded(0);
        $import->setDuplicates(0);
        $import->setFailures(0);

        return $import;
    }

    /**
     * Creates a success import line.
     *
     * @param $import
     * @param $seqId
     * @param $reference
     * @param $message
     * @param $data
     *
     * @return \RealtimeDespatch\OrderFlow\Model\ImportLine
     */
    protected function _createSuccessImportLine($import, $seqId, $reference, $operation, $message, $data = array())
    {
        $import->setSuccesses($import->getSuccesses() + 1);

        return $this->_createImportLine(
            $seqId,
            ImportLineInterface::RESULT_SUCCESS,
            $reference,
            $operation,
            $message,
            $data
        );
    }

    /**
     * Creates a duplicate import line.
     *
     * @param $import
     * @param $seqId
     * @param $reference
     * @param $message
     * @param $data
     *
     * @return \RealtimeDespatch\OrderFlow\Model\ImportLine
     */
    protected function _createDuplicateImportLine($import, $seqId, $reference, $operation, $message, $data = array())
    {
        $import->setDuplicates($import->getDuplicates() + 1);

        return $this->_createImportLine(
            $seqId,
            ImportLineInterface::RESULT_DUPLICATE,
            $reference,
            $operation,
            $message,
            $data
        );
    }

    /**
     * Creates a superseded import line.
     *
     * @param $import
     * @param $seqId
     * @param $reference
     * @param $message
     * @param $data
     *
     * @return \RealtimeDespatch\OrderFlow\Model\ImportLine
     */
    protected function _createSupersededImportLine($import, $seqId, $reference, $operation, $message, $data = array())
    {
        $import->setSuccesses($import->getSuccesses() + 1);

        return $this->_createImportLine(
            $seqId,
            ImportLineInterface::RESULT_SUPERSEDED,
            $reference,
            $operation,
            $message,
            $data
        );
    }

    /**
     * Creates a failure import line.
     *
     * @param $import
     * @param $seqId
     * @param $reference
     * @param $message
     * @param $data
     *
     * @return \RealtimeDespatch\OrderFlow\Model\ImportLine
     */
    protected function _createFailureImportLine($import, $seqId, $reference, $operation, $message, $data = array())
    {
        $import->setFailures($import->getFailures() + 1);

        return $this->_createImportLine(
            $seqId,
            ImportLineInterface::RESULT_FAILURE,
            $reference,
            $operation,
            $message,
            $data
        );
    }

    /**
     * Creates an import line.
     *
     * @param $seqId
     * @param $result
     * @param $reference
     * @param $operation
     * @param $message
     * @param array $data
     *
     * @return \RealtimeDespatch\OrderFlow\Model\ImportLine
     */
    protected function _createImportLine($seqId, $result, $reference, $operation, $message, $data = array())
    {
        $this->_processedIds[$seqId] = $seqId;

        $importLine = $this->_objectManager->create('RealtimeDespatch\OrderFlow\Model\ImportLine');
        $importLine->setSequenceId($seqId);
        $importLine->setResult($result);
        $importLine->setReference($reference);
        $importLine->setOperation($operation);
        $importLine->setEntity($this->getType());
        $importLine->setMessage($message);
        $importLine->setAdditionalData(json_encode($data));
        $importLine->setProcessedAt(date('Y-m-d H:i:s'));

        return $importLine;
    }

    /**
     * Checks for a duplicate import line.
     *
     * @param string $seqId
     *
     * @return boolean
     */
    protected function _isDuplicateLine($seqId)
    {
        $model = $this->_objectManager->create('RealtimeDespatch\OrderFlow\Model\ImportLine');
        $model->load($seqId, 'sequence_id');

        return ! is_null($model->getId());
    }

    /**
     * Checks whether the import line hsa been superseded.
     *
     * @param integer $seqId
     * @param string  $reference
     *
     * @return boolean
     */
    protected function _isSuperseded($seqId, $reference)
    {
        $supersedeLine = $this->_objectManager
            ->create('RealtimeDespatch\OrderFlow\Model\ImportLine')
            ->getCollection()
            ->addFieldToFilter('entity', ['eq' => $this->getType()])
            ->addFieldToFilter('reference', ['eq' => $reference])
            ->setOrder('sequence_id','DESC')
            ->getFirstItem();

        if ( ! $supersedeLine) {
            return false;
        }

        if ($seqId > $supersedeLine->getSequenceId()) {
            return false;
        }

        return $supersedeLine->getSequenceId();
    }
}