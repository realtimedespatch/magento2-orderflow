<?php

namespace RealtimeDespatch\OrderFlow\Plugin\Adminhtml;

class OrderCancellation
{
    const STATUS_CANCELLED = 'Cancelled';
    const STATUS_QUEUED = 'Queued';

    /**
     * @var \RealtimeDespatch\OrderFlow\Helper\Export\Order
     */
    protected $_helper;

    /**
     * @var \RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface
     */
    protected $_builder;

    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    protected $_repository;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \RealtimeDespatch\OrderFlow\Helper\Export\Order $helper
     * @param \RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface $builder
     * @param \Magento\Sales\Model\OrderRepository $repository
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \RealtimeDespatch\OrderFlow\Helper\Export\Order $helper,
        \RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface $builder,
        \Magento\Sales\Model\OrderRepository $repository,
        \Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_helper = $helper;
        $this->_builder = $builder;
        $this->_repository = $repository;
        $this->_objectManager = $objectManager;
    }

    /**
     * Attempts to cancel the order within OrderFlow
     *
     * @param \Magento\Sales\Model\Order $subject
     *
     * @throws Exception
     */
    public function beforeCancel(\Magento\Sales\Model\Order $subject)
    {
        if ( ! $this->_helper->isEnabled($subject->getStore()->getWebsiteId())) {
            throw new \Exception(__('Order exports are currently disabled. Please review the OrderFlow module configuration.'));
        }

        try {
            // Check whether the order can be cancelled.
            if ( ! $subject->canCancel()) {
                return;
            }

            // Orders that are awaiting export cannot be cancelled.
            if ($subject->getOrderflowExportStatus() === self::STATUS_QUEUED) {
                throw new \Exception(__('Cannot cancel an order awaiting export to OrderFlow.'));
            }

            $request = $this->_buildRequest($subject);
            $this->_getRequestProcessor()->process($request);

            if ($this->_repository->get($subject->getId())->getOrderflowExportStatus() !== self::STATUS_CANCELLED) {
                throw new \Exception(__('Order cancellation failed.'));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Retrieves a order from the current request
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface
     */
    protected function _buildRequest($order)
    {
        $this->_builder->setRequestData(
            \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface::TYPE_EXPORT,
            \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface::ENTITY_ORDER,
            \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface::OP_CANCEL
        );

        $this->_builder->addRequestLine(json_encode(array('increment_id' => $order->getIncrementId())));

        return $this->_builder->saveRequest();
    }

    /**
     * Retrieve the request processor instance.
     *
     * @return OrderCancelRequestProcessor
     */
    protected function _getRequestProcessor()
    {
        return $this->_objectManager->create('OrderCancelRequestProcessor');
    }
}
