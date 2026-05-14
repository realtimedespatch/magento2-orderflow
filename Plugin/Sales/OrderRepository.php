<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Plugin\Sales;

use RealtimeDespatch\OrderFlow\Model\Runtime\OrderRepositoryRefreshContext;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\OrderRepositoryFactory;

class OrderRepository
{
    protected OrderExtensionFactory $orderExtensionFactory;
    protected OrderRepositoryRefreshContext $orderRepositoryRefreshContext;
    protected OrderRepositoryFactory $orderRepositoryFactory;

    public function __construct(
        OrderExtensionFactory $orderExtensionFactory,
        OrderRepositoryRefreshContext $orderRepositoryRefreshContext,
        OrderRepositoryFactory $orderRepositoryFactory
    )
    {
        $this->orderExtensionFactory = $orderExtensionFactory;
        $this->orderRepositoryRefreshContext = $orderRepositoryRefreshContext;
        $this->orderRepositoryFactory = $orderRepositoryFactory;
    }

    public function aroundGet(
        OrderRepositoryInterface $subject,
        callable $proceed,
        $id
    ): OrderInterface {
        if ($this->orderRepositoryRefreshContext->isGuardActive()) {
            return $proceed($id);
        }

        if ($this->orderRepositoryRefreshContext->getForcedOrderId() !== (int) $id) {
            return $proceed($id);
        }

        $this->orderRepositoryRefreshContext->setGuardActive(true);

        try {
            return $this->orderRepositoryFactory->create()->get($id);
        } finally {
            $this->orderRepositoryRefreshContext->setGuardActive(false);
        }
    }

    public function afterGet(OrderRepositoryInterface $subject, OrderInterface $order): OrderInterface
    {
        return $this->setOrderFlowExtensionAttributes($order);
    }

    public function afterGetList(
        OrderRepositoryInterface $subject,
        OrderSearchResultInterface $searchResult
    ): OrderSearchResultInterface {
        foreach ($searchResult->getItems() as $order) {
            $this->setOrderFlowExtensionAttributes($order);
        }

        return $searchResult;
    }

    protected function setOrderFlowExtensionAttributes(OrderInterface $order): OrderInterface
    {
        $extensionAttributes = $order->getExtensionAttributes();

        if ($extensionAttributes === null) {
            $extensionAttributes = $this->orderExtensionFactory->create();
        }

        $extensionAttributes->setData('orderflow_export_date', $order->getData('orderflow_export_date'));
        $extensionAttributes->setData('orderflow_export_status', $order->getData('orderflow_export_status'));
        $order->setExtensionAttributes($extensionAttributes);

        return $order;
    }
}
