<?php

namespace RealtimeDespatch\OrderFlow\Model\Service;

use RealtimeDespatch\OrderFlow\Api\OrderRequestManagementInterface;

/**
 * Class OrderRequestService
 *
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class OrderRequestService implements OrderRequestManagementInterface
{
    /**
     * @var \RealtimeDespatch\OrderFlow\Model\Builder\RequestBuilder
     */
    protected $requestBuilder;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Constructor
     *
     * @param \RealtimeDespatch\OrderFlow\Model\Builder\RequestBuilder $requestBuilder
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \RealtimeDespatch\OrderFlow\Model\Builder\RequestBuilder $requestBuilder,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager) {
        $this->requestBuilder = $requestBuilder;
        $this->orderFactory = $orderFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Marks an order as exported.
     *
     * @api
     * @param string $reference
     *
     * @return mixed
     */
    public function export($reference)
    {
        $date = new \DateTime;
        $received = $date->format("Y-m-d H:i:s");

        // Build the request.
        $this->requestBuilder->setRequestData(
            \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface::TYPE_EXPORT,
            \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface::ENTITY_ORDER,
            \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface::OP_EXPORT
        );

        $this->requestBuilder->addRequestLine(json_encode(array('increment_id' => $reference)));

        $order = $this->orderFactory->create()->loadByIncrementId($reference);

        if ($order->getId()) {
            $websiteId = $this->storeManager->getStore($order->getStoreId())->getWebsiteId();
            $this->requestBuilder->setScopeId($websiteId);
        }

        $this->requestBuilder->saveRequest();

        return str_replace(' ','T', $received);
    }
}