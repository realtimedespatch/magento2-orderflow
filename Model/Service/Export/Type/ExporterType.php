<?php

/** @noinspection PhpUndefinedClassInspection */

namespace RealtimeDespatch\OrderFlow\Model\Service\Export\Type;

use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DB\Transaction;
use Psr\Log\LoggerInterface;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use RealtimeDespatch\OrderFlow\Api\ExporterTypeInterface;
use RealtimeDespatch\OrderFlow\Api\Data\ExportInterface;
use RealtimeDespatch\OrderFlow\Api\Data\ExportLineInterface;
use RealtimeDespatch\OrderFlow\Api\Data\ExportInterfaceFactory;
use RealtimeDespatch\OrderFlow\Api\Data\ExportLineInterfaceFactory;
use RealtimeDespatch\OrderFlow\Model\Export;
use RealtimeDespatch\OrderFlow\Model\ExportLine;
use RealtimeDespatch\OrderFlow\Model\Request;

/**
 * Exporter Type.
 *
 * Abstract Base Class for Exporter Types.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
abstract class ExporterType implements ExporterTypeInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $config;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ExportInterfaceFactory
     */
    protected $exportFactory;

    /**
     * @var ExportLineInterfaceFactory
     */
    protected $exportLineFactory;

    /**
     * @var Transaction
     */
    protected $transaction;

    /**
     * @param ScopeConfigInterface $config
     * @param LoggerInterface $logger
     * @param ExportInterfaceFactory $exportFactory
     * @param ExportLineInterfaceFactory $exportLineFactory
     * @param Transaction $transaction
     */
    public function __construct(
        ScopeConfigInterface $config,
        LoggerInterface $logger,
        ExportInterfaceFactory $exportFactory,
        ExportLineInterfaceFactory $exportLineFactory,
        Transaction $transaction
    ) {
        $this->config = $config;
        $this->logger = $logger;
        $this->exportFactory = $exportFactory;
        $this->exportLineFactory = $exportLineFactory;
        $this->transaction = $transaction;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function export(RequestInterface $request)
    {
        $export = $this->createExport($request);

        /* @var Export $export */
        /* @var Request $request */
        $this->transaction->addObject($export);
        $this->transaction->addObject($request);

        foreach ($request->getLines() as $requestLine) {
            /* @var ExportLine $exportLine */
            $exportLine = $this->exportLine($export, $request, $requestLine);
            $export->addLine($exportLine);
            $this->transaction->addObject($exportLine);
            $this->transaction->addObject($requestLine);
        }

        $request->setProcessedAt(date('Y-m-d H:i:s'));
        $this->transaction->save();

        return $export;
    }

    /**
     * Export Request Line.
     *
     * @param ExportInterface $export
     * @param RequestInterface $request
     * @param $requestLine
     *
     * @return mixed
     */
    abstract protected function exportLine(ExportInterface $export, RequestInterface $request, $requestLine);

    /**
     * Create Exports From Request.
     *
     * @param RequestInterface $request
     *
     * @return ExportInterface
     */
    protected function createExport(RequestInterface $request)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $export = $this->exportFactory->create();

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
     * Create Success Export Line.
     *
     * @param ExportInterface $export
     * @param string $reference
     * @param string $operation
     * @param string $message
     * @param object|null $data
     *
     * @return ExportLineInterface
     */
    protected function createSuccessExportLine(
        ExportInterface $export,
        string $reference,
        string $operation,
        string $message,
        object $data = null
    ) {
        $export->setSuccesses($export->getSuccesses() + 1);

        return $this->createExportLine(
            ExportLineInterface::RESULT_SUCCESS,
            $reference,
            $operation,
            $message,
            $data
        );
    }

    /**
     * Create Failure Export Line.
     *
     * @param ExportInterface $export
     * @param string $reference
     * @param string $operation
     * @param string $message
     * @param object|null $data
     *
     * @return ExportLineInterface
     */
    protected function createFailureExportLine(
        ExportInterface $export,
        string $reference,
        string $operation,
        string $message,
        object $data = null
    ) {
        $export->setFailures($export->getFailures() + 1);

        return $this->createExportLine(
            ExportLineInterface::RESULT_FAILURE,
            $reference,
            $operation,
            $message,
            $data
        );
    }

    /**
     * Create Export Line.
     *
     * @param string $result
     * @param string $reference
     * @param string $operation
     * @param string $message
     * @param object|null $data
     *
     * @return ExportLineInterface
     */
    protected function createExportLine(
        string $result,
        string $reference,
        string $operation,
        string $message,
        object $data = null
    ) {
        /** @noinspection PhpUndefinedMethodInspection */
        $exportLine = $this->exportLineFactory->create();

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
