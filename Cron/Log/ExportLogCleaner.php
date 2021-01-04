<?php

/** @noinspection PhpUndefinedClassInspection */

namespace RealtimeDespatch\OrderFlow\Cron\Log;

use RealtimeDespatch\OrderFlow\Helper\Log\Cleaning;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Export\Collection;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Export\CollectionFactory;

/**
 * Export Log Cleaner.
 *
 * Cleanses export logs from the database for performance optimisation.
 */
class ExportLogCleaner
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

        $this->collection->deleteOlderThanCutoff($this->helper->getExportLogExpirationDate());

        return true;
    }
}
