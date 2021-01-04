<?php

namespace RealtimeDespatch\OrderFlow\Cron\Export;

use Magento\Store\Model\ResourceModel\Website\CollectionFactory as WebsiteCollectionFactory;
use Magento\Store\Model\ResourceModel\Website\Collection as WebsiteCollection;
use Magento\Store\Model\Website;
use RealtimeDespatch\OrderFlow\Api\ExportHelperInterface;
use RealtimeDespatch\OrderFlow\Api\RequestProcessorFactoryInterface;

/**
 * Export Cron.
 *
 * Abstract Base Class for the Export Cron Jobs.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class ExportCron
{
    /**
     * @var ExportHelperInterface
     */
    protected $helper;

    /**
     * @var WebsiteCollection
     */
    protected $websites;

    /**
     * @var RequestProcessorFactoryInterface
     */
    protected $reqProcessorFactory;

    /**
     * @param ExportHelperInterface $helper
     * @param RequestProcessorFactoryInterface $requestProcessorFactory
     * @param WebsiteCollectionFactory $websiteCollectionFactory
     */
    public function __construct(
        ExportHelperInterface $helper,
        WebsiteCollectionFactory $websiteCollectionFactory,
        RequestProcessorFactoryInterface $requestProcessorFactory
    ) {
        $this->helper = $helper;
        $this->websites = $websiteCollectionFactory->create();
        $this->reqProcessorFactory = $requestProcessorFactory;
    }

    /**
     * Execute Cron.
     *
     * @return $this|void
     */
    public function execute()
    {
        foreach ($this->websites as $website) {
            $this->executeForWebsite($website);
        }
    }

    /**
     * Execute Cron for Website.
     *
     * @param Website $website
     *
     * @return void
     */
    protected function executeForWebsite(Website $website)
    {
        $websiteId = $website->getId();

        if (! $this->helper->isEnabled($websiteId)) {
            return;
        }

        foreach ($this->helper->getExportableRequests($websiteId) as $request) {
            $requestProcessor = $this->reqProcessorFactory->get($request);
            $requestProcessor->process($request);
        }
    }
}
