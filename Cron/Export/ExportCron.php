<?php

namespace RealtimeDespatch\OrderFlow\Cron\Export;

use Magento\Store\Model\ResourceModel\Website\CollectionFactory as WebsiteCollectionFactory;
use Magento\Store\Model\Website;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use RealtimeDespatch\OrderFlow\Api\ExportHelperInterface;
use RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface;
use RealtimeDespatch\OrderFlow\Api\RequestProcessorFactoryInterface;

/**
 * Export Cron.
 *
 * Abstract Base Class for the Export Cron Jobs.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
abstract class ExportCron
{
    /**
     * @var RequestBuilderInterface
     */
    protected $requestBuilder;

    /**
     * @var RequestProcessorFactoryInterface
     */
    protected $requestProcessorFactory;

    /**
     * @var WebsiteCollectionFactory
     */
    protected $websiteCollectionFactory;

    /**
     * @param RequestBuilderInterface $requestBuilder
     * @param RequestProcessorFactoryInterface $requestProcessorFactory
     * @param WebsiteCollectionFactory $websiteCollectionFactory
     */
    public function __construct(
        RequestBuilderInterface $requestBuilder,
        RequestProcessorFactoryInterface $requestProcessorFactory,
        WebsiteCollectionFactory $websiteCollectionFactory
    ) {
        $this->requestBuilder = $requestBuilder;
        $this->requestProcessorFactory = $requestProcessorFactory;
        $this->websiteCollectionFactory = $websiteCollectionFactory;
    }

    /**
     * Execute Cron.
     *
     * @return $this|void
     */
    public function execute()
    {
        $websites = $this->websiteCollectionFactory->create();

        foreach ($websites as $website) {
            $this->executeForWebsite($website);
        }
    }

    /**
     *  Execute Cron for Website.
     *
     * @param Website $website
     *
     * @return void
     */
    protected function executeForWebsite(Website $website)
    {
        if (! $this->getExportHelper()->isEnabled($website->getId())) {
            return;
        }

        $request = $this->getRequest($website);

        if (! $request) {
            return;
        }

        $requestProcessor = $this->requestProcessorFactory->get($request->getEntity(), $request->getOperation());
        $requestProcessor->process($request);
    }

    /**
     * Helper Getter.
     *
     * @return ExportHelperInterface
     */
    abstract protected function getExportHelper();

    /**
     * Request Getter.
     *
     * @param Website $website
     *
     * @return RequestInterface
     */
    abstract protected function getRequest(Website $website);
}
