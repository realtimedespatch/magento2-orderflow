<?php

namespace RealtimeDespatch\OrderFlow\Cron\Export;

use Magento\Store\Model\ResourceModel\Website\CollectionFactory as WebsiteCollectionFactory;
use Magento\Store\Model\Website;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface;
use RealtimeDespatch\OrderFlow\Api\RequestProcessorFactoryInterface;
use RealtimeDespatch\OrderFlow\Helper\Export\Order as OrderExportHelper;

/**
 * Order Create Export Cron.
 *
 * Cron Job to Export New Orders to OrderFlow.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class OrderCreateExport extends ExportCron
{
    /**
     * @var OrderExportHelper
     */
    protected $helper;

    /**
     * @param OrderExportHelper $helper
     * @param RequestBuilderInterface $requestBuilder
     * @param RequestProcessorFactoryInterface $requestProcessorFactory
     * @param WebsiteCollectionFactory $websiteCollectionFactory
     */
    public function __construct(
        OrderExportHelper $helper,
        RequestBuilderInterface $requestBuilder,
        RequestProcessorFactoryInterface $requestProcessorFactory,
        WebsiteCollectionFactory $websiteCollectionFactory
    ) {
        $this->helper = $helper;

        parent::__construct(
            $requestBuilder,
            $requestProcessorFactory,
            $websiteCollectionFactory
        );
    }

    /**
     * @inheritDoc
     */
    protected function getRequest(Website $website)
    {
        $orders = $this->helper->getCreateableOrders($website);

        if (count($orders) == 0) {
            return null;
        }

        $this->requestBuilder->resetBuilder()->setRequestData(
            RequestInterface::TYPE_EXPORT,
            RequestInterface::ENTITY_ORDER,
            RequestInterface::OP_CREATE
        );

        $this->requestBuilder->setScopeId($website->getId());

        foreach ($orders as $order) {
            $this->requestBuilder->addRequestLine(
                json_encode([
                    'entity_id' => $order->getEntityId(),
                    'increment_id' => $order->getIncrementId(),
                ])
            );
        }

        return $this->requestBuilder->saveRequest();
    }

    /**
     * @inheritDoc
     */
    protected function getExportHelper()
    {
        return $this->helper;
    }
}
