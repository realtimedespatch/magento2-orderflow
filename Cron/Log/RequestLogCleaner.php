<?php

namespace RealtimeDespatch\OrderFlow\Cron\Log;

class RequestLogCleaner
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \RealtimeDespatch\OrderFlow\Model\RequestFactory
     */
    protected $_factory;

    /**
     * @var \RealtimeDespatch\OrderFlow\Helper\Log\Cleaning
     */
    protected $_helper;

    /**
     * RequestCron constructor.
     * @param \Psr\Log\LoggerInterface $logger
     * @param \RealtimeDespatch\OrderFlow\Model\RequestFactory $requestFactory
     * @param \RealtimeDespatch\OrderFlow\Helper\Log\Cleaning $helper
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \RealtimeDespatch\OrderFlow\Model\RequestFactory $factory,
        \RealtimeDespatch\OrderFlow\Helper\Log\Cleaning $helper) {
        $this->_logger = $logger;
        $this->_factory = $factory;
        $this->_helper = $helper;
    }

    /**
     * Executes the request log cleaning job.
     *
     * @return $this|void
     */
    public function execute()
    {
        if ( ! $this->_helper->isEnabled()) {
            return;
        }

        $cutoff = date('Y-m-d', strtotime('-'.($this->_helper->getRequestLogDuration() - 1).' days'));

        $this->_factory
            ->create()
            ->getCollection()
            ->addFieldToFilter('created_at', ['lteq' => $cutoff])
            ->walk('delete');
    }
}