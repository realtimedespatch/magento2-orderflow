<?php

/** @noinspection PhpUndefinedClassInspection */

namespace RealtimeDespatch\OrderFlow\Model\Service\Import\Type;

use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DB\Transaction;
use Psr\Log\LoggerInterface;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use RealtimeDespatch\OrderFlow\Api\ImporterTypeInterface;
use RealtimeDespatch\OrderFlow\Api\Data\ImportInterface;
use RealtimeDespatch\OrderFlow\Api\Data\ImportLineInterface;
use RealtimeDespatch\OrderFlow\Api\Data\ImportInterfaceFactory;
use RealtimeDespatch\OrderFlow\Api\Data\ImportLineInterfaceFactory;
use RealtimeDespatch\OrderFlow\Model\Import;
use RealtimeDespatch\OrderFlow\Model\ImportLine;
use RealtimeDespatch\OrderFlow\Model\Request;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\ImportLine\CollectionFactory as ImportLineCollectionFactory;

/**
 * Importer Type.
 *
 * Abstract Base Class for Importer Types.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
abstract class ImporterType implements ImporterTypeInterface
{
    /**
     * @var array
     */
    protected $processedIds = [];

    /**
     * @var ScopeConfigInterface
     */
    protected $config;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ImportInterfaceFactory
     */
    protected $importFactory;

    /**
     * @var ImportLineInterfaceFactory
     */
    protected $importLineFactory;

    /**
     * @var ImportLineCollectionFactory
     */
    protected $importLineCollectionFactory;

    /**
     * @var Transaction
     */
    protected $transaction;

    /**
     * @param ScopeConfigInterface $config
     * @param LoggerInterface $logger
     * @param ImportInterfaceFactory $importFactory
     * @param ImportLineInterfaceFactory $importLineFactory
     * @param ImportLineCollectionFactory $importLineCollectionFactory
     * @param Transaction $transaction
     */
    public function __construct(
        ScopeConfigInterface $config,
        LoggerInterface $logger,
        ImportInterfaceFactory $importFactory,
        ImportLineInterfaceFactory $importLineFactory,
        ImportLineCollectionFactory $importLineCollectionFactory,
        Transaction $transaction
    ) {
        $this->config = $config;
        $this->logger = $logger;
        $this->importFactory = $importFactory;
        $this->importLineFactory = $importLineFactory;
        $this->importLineCollectionFactory = $importLineCollectionFactory;
        $this->transaction = $transaction;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function import(RequestInterface $request)
    {
        $import = $this->createImport($request);

        /* @var Import $import */
        /* @var Request $request */
        $this->transaction->addObject($import);
        $this->transaction->addObject($request);

        foreach ($request->getLines() as $requestLine) {
            $importLine = $this->importLine($import, $request, $requestLine);
            $importLine->setImport($import);
            $this->transaction->addObject($importLine);
            $this->transaction->addObject($requestLine);
        }

        $request->setProcessedAt(date('Y-m-d H:i:s'));
        $this->transaction->save();
    }

    /**
     * Import Request Line.
     *
     * @param ImportInterface $import
     * @param RequestInterface $request
     * @param $requestLine
     * @return mixed
     * @api
     */
    abstract protected function importLine(ImportInterface $import, RequestInterface $request, $requestLine);

    /**
     * Create Import From Request.
     *
     * @param RequestInterface $request
     *
     * @return ImportInterface
     */
    protected function createImport(RequestInterface $request)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $import = $this->importFactory->create();

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
     * Create Success Import Line.
     *
     * @param ImportInterface $import
     * @param string $seqId
     * @param string $reference
     * @param string $operation
     * @param string $message
     * @param array $data
     *
     * @return ImportLineInterface
     */
    protected function createSuccessImportLine(
        ImportInterface $import,
        string $seqId,
        string $reference,
        string $operation,
        string $message,
        array $data = []
    ) {
        $import->setSuccesses($import->getSuccesses() + 1);

        return $this->createImportLine(
            $seqId,
            ImportLineInterface::RESULT_SUCCESS,
            $reference,
            $operation,
            $message,
            $data
        );
    }

    /**
     * Create Duplicate Import Line.
     *
     * @param ImportInterface $import
     * @param string $seqId
     * @param string $reference
     * @param string $operation
     * @param string $message
     * @param array $data
     *
     * @return ImportLineInterface
     */
    protected function createDuplicateImportLine(
        ImportInterface $import,
        string $seqId,
        string $reference,
        string $operation,
        string $message,
        array $data = []
    ) {
        $import->setDuplicates($import->getDuplicates() + 1);

        return $this->createImportLine(
            $seqId,
            ImportLineInterface::RESULT_DUPLICATE,
            $reference,
            $operation,
            $message,
            $data
        );
    }

    /**
     * Create Superseded Import Line.
     *
     * @param ImportInterface $import
     * @param string $seqId
     * @param string $reference
     * @param string $operation
     * @param string $message
     * @param array $data
     *
     * @return ImportLineInterface
     */
    protected function createSupersededImportLine(
        ImportInterface $import,
        string $seqId,
        string $reference,
        string $operation,
        string $message,
        array $data = []
    ) {
        $import->setSuccesses($import->getSuccesses() + 1);

        return $this->createImportLine(
            $seqId,
            ImportLineInterface::RESULT_SUPERSEDED,
            $reference,
            $operation,
            $message,
            $data
        );
    }

    /**
     * Create Failure Import Line.
     *
     * @param ImportInterface $import
     * @param string $seqId
     * @param string $reference
     * @param string $operation
     * @param string $message
     * @param array $data
     *
     * @return ImportLineInterface
     */
    protected function createFailureImportLine(
        ImportInterface $import,
        string $seqId,
        string $reference,
        string $operation,
        string $message,
        array $data = []
    ) {
        $import->setFailures($import->getFailures() + 1);

        return $this->createImportLine(
            $seqId,
            ImportLineInterface::RESULT_FAILURE,
            $reference,
            $operation,
            $message,
            $data
        );
    }

    /**
     * Create Import Line.
     *
     * @param string $seqId
     * @param string $result
     * @param string $reference
     * @param string $operation
     * @param string $message
     * @param array $data
     *
     * @return ImportLineInterface
     */
    protected function createImportLine(
        string $seqId,
        string $result,
        string $reference,
        string $operation,
        string $message,
        array $data = []
    ) {
        $this->processedIds[$seqId] = $seqId;

        /** @noinspection PhpUndefinedMethodInspection */
        $importLine = $this->importLineFactory->create();

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
    protected function isDuplicateLine(string $seqId)
    {
        /** @var ImportLine $duplicateLine */
        $duplicateLine = $this
            ->importLineCollectionFactory
            ->create()
            ->addFieldToFilter('sequence_id', ['eq' => $seqId])
            ->addFieldToFilter('result', ['eq' => ImportLineInterface::RESULT_SUCCESS])
            ->setOrder('line_id', 'DESC')
            ->getFirstItem();

        if (!$duplicateLine) {
            return false;
        }

        return $duplicateLine->getId() !== null;
    }

    /**
     * Checks whether the import line has been superseded.
     *
     * @param integer $seqId
     * @param string $reference
     *
     * @return boolean
     */
    protected function isSuperseded(int $seqId, string $reference)
    {
        /** @var ImportLine $supersededLine */
        $supersededLine = $this
            ->importLineCollectionFactory
            ->create()
            ->addFieldToFilter('entity', ['eq' => $this->getType()])
            ->addFieldToFilter('reference', ['eq' => $reference])
            ->addFieldToFilter('result', ['eq' => ImportLineInterface::RESULT_SUCCESS])
            ->setOrder('sequence_id', 'DESC')
            ->getFirstItem();

        if (! $supersededLine) {
            return false;
        }

        if ($seqId > $supersededLine->getSequenceId()) {
            return false;
        }

        return $supersededLine->getSequenceId();
    }
}
