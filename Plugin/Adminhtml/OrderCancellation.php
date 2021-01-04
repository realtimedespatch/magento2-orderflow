<?php

namespace RealtimeDespatch\OrderFlow\Plugin\Adminhtml;

use Exception;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface;
use RealtimeDespatch\OrderFlow\Helper\Export\Order as OrderHelper;
use RealtimeDespatch\OrderFlow\Model\Service\Request\RequestProcessor;
use RealtimeDespatch\OrderFlow\Model\Source\Export\Status as ExportStatus;

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
            throw new LocalizedException(__('Order cancellation failed - the order cannot be cancelled.'));
        }

        // Orders that are queued for export cannot be cancelled.
        if ($order->getData('orderflow_export_status') === ExportStatus::STATUS_QUEUED) {
            throw new LocalizedException(__('Order cancellation failed - the order is already queued for export.'));
        }

        // Build, and process the cancellation request.
        try {
            $request = $this->buildRequest($order);
            $this->requestProcessor->process($request);
        } catch (Exception $ex) {
            throw new LocalizedException(__('Order cancellation failed - please try again.'));
        }

        // Check the order has been cancelled.
        if (! $this->isOrderCancelled($order)) {
            throw new LocalizedException(__('Order cancellation failed - please try again.'));
        }
    }

    /**
     * Checks whether the order has been cancelled.
     *
     * @param Order $order
     * @return bool
     */
    protected function isOrderCancelled(Order $order): bool
    {
        $order = $this->orderRepository->get($order->getId());

        return $order->getData('orderflow_export_status') === ExportStatus::STATUS_CANCELLED;
    }

    /**
     * Builds an order cancellation export request from the order.
     *
     * @param Order $order
     * @return RequestInterface
     * @throws CouldNotSaveException
     */
    protected function buildRequest(Order $order): RequestInterface
    {
        $messageId = null;

        return $this->requestBuilder->saveRequest(
            RequestInterface::TYPE_EXPORT,
            RequestInterface::ENTITY_ORDER,
            RequestInterface::OP_CANCEL,
            $messageId,
            [json_encode(['increment_id' => $order->getIncrementId()])]
        );
    }
}
