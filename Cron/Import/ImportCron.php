<?php

/** @noinspection PhpUndefinedClassInspection */

namespace RealtimeDespatch\OrderFlow\Cron\Import;

use RealtimeDespatch\OrderFlow\Api\ImportHelperInterface;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Request\Collection;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Request\CollectionFactory as RequestCollectionFactory;
use RealtimeDespatch\OrderFlow\Api\RequestProcessorFactoryInterface;

/**
 * Import Cron.
 *
 * Abstract Base Class for the Import Cron Jobs.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
abstract class ImportCron
{
    /**
     * @var RequestCollectionFactory
     */
    protected $requestCollectionFactory;

    /**
     * @var RequestProcessorFactoryInterface
     */
    protected $requestProcessorFactory;

    /**
     * @param RequestCollectionFactory $requestCollectionFactory
     * @param RequestProcessorFactoryInterface $requestProcessorFactory
     */
    public function __construct(
        RequestCollectionFactory $requestCollectionFactory,
        RequestProcessorFactoryInterface $requestProcessorFactory
    ) {
        $this->requestCollectionFactory = $requestCollectionFactory;
        $this->requestProcessorFactory = $requestProcessorFactory;
    }

    /**
     * Execute Cron.
     *
     * @return $this|void
     */
    public function execute()
    {
        if (! $this->getHelper()->isEnabled()) {
            return;
        }

        foreach ($this->getImportableRequests() as $request) {
            $requestProcessor = $this->requestProcessorFactory->get($request->getEntity(), $request->getOperation());
            $requestProcessor->process($request);
        }
    }

    /**
     * Importable Requests Getter.
     *
     * @return Collection
     */
    protected function getImportableRequests()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this
            ->requestCollectionFactory
            ->create()
            ->addFieldToFilter('type', ['eq' => 'Import'])
            ->addFieldToFilter('entity', ['eq' => $this->getEntityType()])
            ->addFieldToFilter('processed_at', ['null' => true])
            ->setOrder('message_id', 'ASC')
            ->setPageSize($this->getHelper()->getBatchSize())
            ->setCurPage(1);
    }

    /**
     * Helper Getter.
     *
     * @return ImportHelperInterface
     */
    abstract protected function getHelper();

    /**
     * Entity Type Getter.
     *
     * @return string
     */
    abstract protected function getEntityType();
}
