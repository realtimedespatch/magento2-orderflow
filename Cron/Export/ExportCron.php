<?php

namespace RealtimeDespatch\OrderFlow\Cron\Export;

abstract class ExportCron
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface
     */
    protected $_requestBuilder;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Store\Model\WebsiteFactory
     */
    protected $_websiteFactory;

    /**
     * ExportCron constructor.
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param \RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface $requestBuilder
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Store\Model\WebsiteFactory $websiteFactory
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface $requestBuilder,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\WebsiteFactory $websiteFactory) {
        $this->_logger = $logger;
        $this->_requestBuilder = $requestBuilder;
        $this->_objectManager = $objectManager;
        $this->_websiteFactory = $websiteFactory;
    }

    /**
     * Executes the inventory export job
     *
     * @return $this|void
     */
    public function execute()
    {
        $websites = $this->_websiteFactory->create()->getCollection();

        foreach ($websites as $website) {
            $this->_execute($website);
        }
    }

    /**
     * Executes the cron job for a specific website.
     *
     * @param mixed $website
     *
     * @return void
     */
    protected function _execute($website)
    {
        if ( ! $this->_getHelper()->isEnabled($website->getId())) {
            return;
        }

        $request = $this->_getRequest($website);

        if ( ! $request) {
            return;
        }

        $this->_getProcessor($request->getEntity(), $request->getOperation())->process($request);
    }

    /**
     * Retrieve the request processor instance.
     *
     * @param string $entity Entity Type
     * @param string $operation Operation Type
     *
     * @return RealtimeDespatch\OrderFlow\Model\Service\Request\RequestProcessor
     */
    protected function _getProcessor($entity, $operation)
    {
        return $this->_objectManager->create($entity.$operation.'RequestProcessor');
    }

    /**
     * Returns the request to process.
     *
     * @param \Magento\Store\Model\Website $website
     *
     * @return array
     */
    protected abstract function _getRequest($website);

    /**
     * Returns the appropriate cron helper.
     *
     * @return \Magento\Framework\App\Helper\AbstractHelper
     */
    protected abstract function _getHelper();
}