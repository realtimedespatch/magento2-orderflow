<?php

namespace RealtimeDespatch\OrderFlow\Controller\Adminhtml\Order;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface;
use RealtimeDespatch\OrderFlow\Helper\Export\Order as OrderExportHelper;
use RealtimeDespatch\OrderFlow\Model\Service\Request\RequestProcessor;

/**
 * Order Export Controller.
 *
 * Handles the request to queue an order for export to OrderFlow.
 */
class Export extends Action
{
    /**
     * @var OrderExportHelper
     */
    protected $helper;

    /**
     * @var RequestProcessor
     */
    protected $requestProcessor;

    /**
     * @var RequestBuilderInterface
     */
    protected $requestBuilder;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Context $context
     * @param OrderExportHelper $helper
     * @param RequestProcessor $requestProcessor
     * @param RequestBuilderInterface $requestBuilder
     * @param OrderRepositoryInterface $orderRepository
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        OrderExportHelper $helper,
        RequestProcessor $requestProcessor,
        RequestBuilderInterface $requestBuilder,
        OrderRepositoryInterface $orderRepository,
        StoreManagerInterface $storeManager
    ) {
        $this->helper = $helper;
        $this->requestProcessor = $requestProcessor;
        $this->requestBuilder = $requestBuilder;
        $this->orderRepository = $orderRepository;
        $this->storeManager = $storeManager;

        parent::__construct($context);
    }

    /**
     * Execute.
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if (! $this->helper->isEnabled()) {
            $this->messageManager->addErrorMessage(
                __('Order exports are disabled. Please review the OrderFlow module configuration.')
            );

            return $resultRedirect->setRefererUrl();
        }

        try {
            $order = $this->getOrder();

            if (! $order) {
                return $resultRedirect->setRefererUrl();
            }

            if ($order->getIsVirtual()) {
                $this->messageManager->addErrorMessage(
                    __('Virtual orders cannot be exported.')
                );

                return $resultRedirect->setRefererUrl();
            }

            $request = $this->buildRequest($order);
            $export = $this->requestProcessor->process($request);

            if ($export->getFailures() || $export->getDuplicates()) {
                $this->messageManager->addErrorMessage(__('Order export failed.'));
            } else {
                $this->messageManager->addSuccessMessage(__('Order successfully queued for export.'));
            }
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setRefererUrl();
    }

    /**
     * Order Getter.
     *
     * @return OrderInterface|boolean
     */
    protected function getOrder()
    {
        $orderId = $this->getRequest()->getParam('order_id');

        try {
            $order = $this->orderRepository->get($orderId);
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Order with ID: '.$orderId.' cannot be retrieved.')
            );
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return false;
        }

        return $order;
    }

    /**
     * Build Export Request.
     *
     * @param OrderInterface $order
     *
     * @return RequestInterface
     * @throws NoSuchEntityException
     */
    protected function buildRequest(OrderInterface $order)
    {
        $this->requestBuilder->setRequestData(
            RequestInterface::TYPE_EXPORT,
            RequestInterface::ENTITY_ORDER,
            RequestInterface::OP_CREATE
        );

        $this->requestBuilder->setScopeId($this->getWebsiteId($order));
        $this->requestBuilder->addRequestLine(
            json_encode([
                'entity_id' => $order->getEntityId(),
                'increment_id' => $order->getIncrementId(),
            ])
        );

        return $this->requestBuilder->saveRequest();
    }

    /**
     * Website ID Getter.
     *
     * @param OrderInterface $order
     *
     * @return integer
     * @throws NoSuchEntityException
     */
    protected function getWebsiteId(OrderInterface $order)
    {
        return $this->storeManager->getStore($order->getStoreId())->getWebsiteId();
    }
}
