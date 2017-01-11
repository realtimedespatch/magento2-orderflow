<?php

namespace RealtimeDespatch\OrderFlow\Cron\Import;

abstract class ImportCron
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \RealtimeDespatch\OrderFlow\Model\RequestFactory
     */
    protected $_requestFactory;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * ImportCron constructor.
     * @param \Psr\Log\LoggerInterface $logger
     * @param \RealtimeDespatch\OrderFlow\Model\RequestFactory $requestFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \RealtimeDespatch\OrderFlow\Model\RequestFactory $requestFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager) {
        $this->_logger = $logger;
        $this->_requestFactory = $requestFactory;
        $this->_objectManager = $objectManager;
    }

    /**
     * Executes the inventory import job
     *
     * @return $this|void
     */
    public function execute()
    {
        if ( ! $this->_getHelper()->isEnabled()) {
            return;
        }

        foreach ($this->_getImportableRequests() as $request) {
            $this->_getProcessor($request->getEntity(), $request->getOperation())->process($request);
        }
    }

    /**
     * Returns the requests that are ready to process.
     *
     * @return array
     */
    protected function _getImportableRequests()
    {
        return $this->_requestFactory
            ->create()
            ->getCollection()
            ->addFieldToFilter('type', ['eq' => 'Import'])
            ->addFieldToFilter('entity', ['eq' => $this->_getEntityType()])
            ->addFieldToFilter('processed_at', ['null' => true])
            ->setOrder('message_id','ASC')
            ->setPageSize($this->_getHelper()->getBatchSize())
            ->setCurPage(1);
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
     * Returns the appropriate cron helper.
     *
     * @return \Magento\Framework\App\Helper\AbstractHelper
     */
    protected abstract function _getHelper();

    /**
     * Returns the import entity type.
     *
     * @return string
     */
    protected abstract function _getEntityType();
}