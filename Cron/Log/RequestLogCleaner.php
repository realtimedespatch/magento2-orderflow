<?php

/** @noinspection PhpUndefinedClassInspection */

namespace RealtimeDespatch\OrderFlow\Cron\Log;

use Psr\Log\LoggerInterface;
use RealtimeDespatch\OrderFlow\Helper\Log\Cleaning;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Request\CollectionFactory;

/**
 * Request Log Cleaner.
 *
 * Cleanses export logs from the database for performance optimisation.
 */
class RequestLogCleaner
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var CollectionFactory
     */
    protected $factory;

    /**
     * @var Cleaning
     */
    protected $helper;

    /**
     * @param LoggerInterface $logger
     * @param CollectionFactory $factory
     * @param Cleaning $helper
     */
    public function __construct(
        LoggerInterface $logger,
        CollectionFactory $factory,
        Cleaning $helper
    ) {
        $this->logger = $logger;
        $this->factory = $factory;
        $this->helper = $helper;
    }

    /**
     * Executes the request log cleaning job.
     *
     * @return $this|void
     */
    public function execute()
    {
        if (! $this->helper->isEnabled()) {
            return;
        }

        $cutoff = date('Y-m-d', strtotime('-'.($this->helper->getRequestLogDuration() - 1).' days'));

        /** @noinspection PhpUndefinedMethodInspection */
        $this->factory
            ->create()
            ->addFieldToFilter('created_at', ['lteq' => $cutoff])
            ->walk('delete');
    }
}
