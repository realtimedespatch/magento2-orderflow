<?php

namespace RealtimeDespatch\OrderFlow\Cron\Export;

use Magento\Store\Model\ResourceModel\Website\CollectionFactory as WebsiteCollectionFactory;
use Magento\Store\Model\Website;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface;
use RealtimeDespatch\OrderFlow\Api\RequestProcessorFactoryInterface;
use RealtimeDespatch\OrderFlow\Helper\Export\Product as ProductExportHelper;

/**
 * Product Create Export Cron.
 *
 * Cron Job to Export New Products to OrderFlow.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class ProductCreateExport extends ExportCron
{
    /**
     * @var ProductExportHelper
     */
    protected $helper;

    /**
     * @param ProductExportHelper $helper
     * @param RequestBuilderInterface $requestBuilder
     * @param RequestProcessorFactoryInterface $requestProcessorFactory
     * @param WebsiteCollectionFactory $websiteCollectionFactory
     */
    public function __construct(
        ProductExportHelper $helper,
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
        $products = $this->helper->getCreateableProducts($website);

        if (count($products) == 0) {
            return null;
        }

        $this->requestBuilder->resetBuilder()->setRequestData(
            RequestInterface::TYPE_EXPORT,
            RequestInterface::ENTITY_PRODUCT,
            RequestInterface::OP_CREATE
        );

        $this->requestBuilder->setScopeId($website->getId());

        foreach ($products as $product) {
            $this->requestBuilder->addRequestLine(json_encode(['sku' => $product->getSku()]));
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
