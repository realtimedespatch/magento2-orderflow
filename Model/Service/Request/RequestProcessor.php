<?php

namespace RealtimeDespatch\OrderFlow\Model\Service\Request;

use \RealtimeDespatch\OrderFlow\Model\Request;

class RequestProcessor
{
    /**
     * @var \RealtimeDespatch\OrderFlow\Api\RequestProcessorTypeInterface
     */
    protected $_type;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @param \RealtimeDespatch\OrderFlow\Api\RequestProcessorTypeInterface $type
     */
    public function __construct(
        \RealtimeDespatch\OrderFlow\Api\RequestProcessorTypeInterface $type,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Event\ManagerInterface $eventManager
    )
    {
        $this->_type = $type;
        $this->_logger = $logger;
        $this->_eventManager = $eventManager;
    }

    /**
     * Processes a request.
     *
     * @param \RealtimeDespatch\OrderFlow\Model\Request $request
     *
     * @return void
     */
    public function process(Request $request)
    {
        if ( ! $request->canProcess()) {
            return false;
        }

        return $this->_type->process($request);
    }
}