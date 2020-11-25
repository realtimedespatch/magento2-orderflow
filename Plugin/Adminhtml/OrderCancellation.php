<?php

namespace RealtimeDespatch\OrderFlow\Plugin\Adminhtml;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface;
use RealtimeDespatch\OrderFlow\Helper\Export\Order as OrderHelper;
use RealtimeDespatch\OrderFlow\Model\Service\Request\RequestProcessor;

/**
 * Order Cancellation Plugin
 *
 * Captures the details of an order cancellation request.
 *
 * This occurs when Magento makes a call to OrderFlow to cancel an order.
 */
class OrderCancellation
{
    const STATUS_CANCELLED = 'Cancelled';
    const STATUS_QUEUED = 'Queued';

    /**
     * @var OrderHelper
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
     * @param OrderHelper $helper
     * @param RequestProcessor $requestProcessor
     * @param RequestBuilderInterface $requestBuilder
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        OrderHelper $helper,
        RequestProcessor $requestProcessor,
        RequestBuilderInterface $requestBuilder,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->helper = $helper;
        $this->requestProcessor = $requestProcessor;
        $this->requestBuilder = $requestBuilder;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Capture Order Cancellation Request Details.
     *
     * @param Order $order
     * @throws LocalizedException
     */
    public function beforeCancel(Order $order)
    {
        if (! $this->helper->isEnabled()) {
            throw new LocalizedException(
                __('Order Exports are Disabled. Please review the OrderFlow module configuration.')
            );
        }

        // Check whether the order can be cancelled.
        if (! $order->canCancel()) {
            return;
        }

        // Orders that are awaiting export cannot be cancelled.
        /** @noinspection PhpUndefinedMethodInspection */
        if ($order->getOrderflowExportStatus() === self::STATUS_QUEUED) {
            throw new LocalizedException(__('Order Cancellation Failed - The Order is Pending Export to OrderFlow.'));
        }

        $request = $this->buildRequest($order);
        $this->requestProcessor->process($request);

        /** @noinspection PhpUndefinedMethodInspection */
        if ($this->orderRepository->get($order->getId())->getOrderflowExportStatus() !== self::STATUS_CANCELLED) {
            throw new LocalizedException(__('Order Cancellation Failed - Please Try Again.'));
        }
    }

    /**
     * Builds an order cancellation export request from the order.
     *
     * @param Order $order
     * @return RequestInterface
     */
    protected function buildRequest(Order $order)
    {
        $this->requestBuilder->setRequestData(
            RequestInterface::TYPE_EXPORT,
            RequestInterface::ENTITY_ORDER,
            RequestInterface::OP_CANCEL
        );

        $this->requestBuilder->addRequestLine(json_encode(['increment_id' => $order->getIncrementId()]));

        return $this->requestBuilder->saveRequest();
    }
}
