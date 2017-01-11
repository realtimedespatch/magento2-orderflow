<?php

namespace RealtimeDespatch\OrderFlow\Cron\Log;

class ExportLogCleaner
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \RealtimeDespatch\OrderFlow\Model\ExportFactory
     */
    protected $_factory;

    /**
     * @var \RealtimeDespatch\OrderFlow\Helper\Log\Cleaning
     */
    protected $_helper;

    /**
     * ImportCron constructor.
     * @param \Psr\Log\LoggerInterface $logger
     * @param \RealtimeDespatch\OrderFlow\Model\ExportFactory $requestFactory
     * @param \RealtimeDespatch\OrderFlow\Helper\Log\Cleaning $helper
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \RealtimeDespatch\OrderFlow\Model\ExportFactory $factory,
        \RealtimeDespatch\OrderFlow\Helper\Log\Cleaning $helper) {
        $this->_logger = $logger;
        $this->_factory = $factory;
        $this->_helper = $helper;
    }

    /**
     * Executes the export log cleaning job.
     *
     * @return $this|void
     */
    public function execute()
    {
        if ( ! $this->_helper->isEnabled()) {
            return;
        }

        $cutoff = date('Y-m-d', strtotime('-'.($this->_helper->getExportLogDuration() - 1).' days'));

        $this->_factory
            ->create()
            ->getCollection()
            ->addFieldToFilter('created_at', ['lteq' => $cutoff])
            ->walk('delete');
    }
}