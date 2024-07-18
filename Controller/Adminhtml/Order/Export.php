<?php

namespace RealtimeDespatch\OrderFlow\Controller\Adminhtml\Order;

use Magento\Backend\App\Action\Context;

class Export extends \Magento\Backend\App\Action
{
    /**
     * @var \RealtimeDespatch\OrderFlow\Helper\Export\Order
     */
    protected $_exportHelper;

    /**
     * @var \RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface
     */
    protected $_builder;

    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    protected $_repository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param Context $context
     * @param \RealtimeDespatch\OrderFlow\Helper\Export\Order $helper
     * @param \RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface $builder
     * @param \Magento\Sales\Model\OrderRepository $repository
     *
     */
    public function __construct(
        Context $context,
        \RealtimeDespatch\OrderFlow\Helper\Export\Order $helper,
        \RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface $builder,
        \Magento\Sales\Model\OrderRepository $repository,
        \Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        $this->_exportHelper = $helper;
        $this->_builder = $builder;
        $this->_repository = $repository;
        $this->_storeManager = $storeManager;

        parent::__construct($context);
    }

    /**
     * Export Action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $order = $this->_getOrder();

            if ( ! $order) {
                return $resultRedirect->setRefererUrl();
            }

            // Check whether order exports are enabled.
            if ( ! $this->_exportHelper->isEnabled($order->getStore()->getWebsiteId())) {
                $this->messageManager->addError(__('Order exports are currently disabled. Please review the OrderFlow module configuration.'));
                return $resultRedirect->setRefererUrl();
            }

            if ($order->getIsVirtual()) {
                $this->messageManager->addError(__('You cannot export a virtual order to OrderFlow.'));
                return $resultRedirect->setRefererUrl();
            }

            $request = $this->_buildRequest($order);

            $export = $this->_getRequestProcessor()->process($request);

            if ($export->getFailures() || $export->getDuplicates()) {
                $this->messageManager->addError(__('Order '.$order->getIncrementId().' has failed to be queued for export to OrderFlow.'));
            } else {
                $this->messageManager->addSuccess(__('Order '.$order->getIncrementId().' has been queued for export to OrderFlow.'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

        return $resultRedirect->setRefererUrl();
    }

    /**
     * Retrieves a order from the current request
     *
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    protected function _getOrder()
    {
        $id = $this->getRequest()->getParam('order_id');

        try {
            $order = $this->_repository->get($id);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addError(__('Order Not Found.'));
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return false;
        } catch (InputException $e) {
            $this->messageManager->addError(__('Order Not Found.'));
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return false;
        }

        return $order;
    }

    /**
     * Retrieves a order from the current request
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface
     */
    protected function _buildRequest($order)
    {
        $this->_builder->setRequestData(
            \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface::TYPE_EXPORT,
            \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface::ENTITY_ORDER,
            \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface::OP_CREATE
        );

        $this->_builder->setScopeId($this->_getWebsiteId($order));
        $this->_builder->addRequestLine(
            json_encode(array(
                'entity_id' => $order->getEntityId(),
                'increment_id' => $order->getIncrementId(),
            ))
        );

        return $this->_builder->saveRequest();
    }

    /**
     * Retrieve the request processor instance.
     *
     * @return RealtimeDespatch\OrderFlow\Model\Service\Request\RequestProcessor
     */
    protected function _getRequestProcessor()
    {
        return $this->_objectManager->create('OrderCreateRequestProcessor');
    }

    /**
     * Returns the current website ID.
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     *
     * @return integer
     */
    protected function _getWebsiteId($order)
    {
        return $this->_storeManager->getStore($order->getStoreId())->getWebsiteId();
    }
}
