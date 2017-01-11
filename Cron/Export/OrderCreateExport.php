<?php

namespace RealtimeDespatch\OrderFlow\Cron\Export;

class OrderCreateExport extends \RealtimeDespatch\OrderFlow\Cron\Export\ExportCron
{
    /**
     * @var \RealtimeDespatch\OrderFlow\Helper\Export\Order
     */
    protected $_helper;

    /**
     * OrderExport constructor.
     * @param \RealtimeDespatch\OrderFlow\Helper\Export\Order $helper
     * @param \Psr\Log\LoggerInterface $logger
     * @param \RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface $requestBuilder
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Store\Model\WebsiteFactory $websiteFactory
     */
    public function __construct(
        \RealtimeDespatch\OrderFlow\Helper\Export\Order $helper,
        \Psr\Log\LoggerInterface $logger,
        \RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface $requestBuilder,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\WebsiteFactory $websiteFactory) {
        $this->_helper = $helper;
        parent::__construct($logger, $requestBuilder, $objectManager, $websiteFactory);
    }

    /**
     * Returns the request set to process.
     *
     * @param \Magento\Store\Model\Website $website
     *
     * @return array
     */
    protected function _getRequest($website)
    {
        $orders = $this->_helper->getCreateableOrders($website);

        if (count($orders) == 0) {
            return null;
        }

        $this->_requestBuilder->resetBuilder()->setRequestData(
            \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface::TYPE_EXPORT,
            \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface::ENTITY_ORDER,
            \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface::OP_CREATE
        );

        $this->_requestBuilder->setScopeId($website->getId());

        foreach ($orders as $order) {
            $this->_requestBuilder->addRequestLine(json_encode(array('increment_id' => $order->getIncrementId())));
        }

        return $this->_requestBuilder->saveRequest();
    }

    /**
     * Returns the import entity type.
     *
     * @return \Magento\Framework\App\Helper\AbstractHelper
     */
    protected function _getHelper()
    {
        return $this->_helper;
    }
}