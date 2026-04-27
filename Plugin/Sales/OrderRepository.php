<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Plugin\Sales;

use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class OrderRepository
{
    protected OrderExtensionFactory $orderExtensionFactory;

    public function __construct(OrderExtensionFactory $orderExtensionFactory)
    {
        $this->orderExtensionFactory = $orderExtensionFactory;
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
