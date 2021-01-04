<?php

/** @noinspection PhpUndefinedClassInspection */

namespace RealtimeDespatch\OrderFlow\Cron\Log;

use RealtimeDespatch\OrderFlow\Helper\Log\Cleaning;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Request\Collection;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Request\CollectionFactory;

/**
 * Request Log Cleaner.
 *
 * Cleanses request logs from the database for performance optimisation.
 */
class RequestLogCleaner
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var Cleaning
     */
    protected $helper;

    /**
     * @param CollectionFactory $factory
     * @param Cleaning $helper
     */
    public function __construct(
        CollectionFactory $factory,
        Cleaning $helper
    ) {
        $this->collection = $factory->create();
        $this->helper = $helper;
    }

    /**
     * Executes the export log cleaning job.
     *
     * @return boolean
     */
    public function execute()
    {
        if (! $this->helper->isEnabled()) {
            return false;
        }

        $this->collection->deleteOlderThanCutoff($this->helper->getRequestLogExpirationDate());

        return true;
    }
}
