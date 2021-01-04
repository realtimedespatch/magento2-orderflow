<?php

namespace RealtimeDespatch\OrderFlow\Cron\Builder\Export;

use RealtimeDespatch\OrderFlow\Api\ExportRequestBuilderInterface;
use Magento\Store\Model\ResourceModel\Website\CollectionFactory as WebsiteCollectionFactory;
use Magento\Store\Model\ResourceModel\Website\Collection as WebsiteCollection;

/**
 * Export Request Builder Cron.
 *
 * Periodically generates new export requests from pending products and orders.
 */
class ExportRequestBuilderCron
{
    /**
     * @var WebsiteCollection
     */
    protected $websites;

    /**
     * @var array
     */
    protected $reqBuilders;

    /**
     * @param WebsiteCollectionFactory $websiteCollectionFactory
     * @param array $reqBuilders
     */
    public function __construct(
        WebsiteCollectionFactory $websiteCollectionFactory,
        array $reqBuilders = []
    ) {
        $this->reqBuilders = $reqBuilders;
        $this->websites = $websiteCollectionFactory->create();
    }

    /**
     * Execute Cron.
     *
     * @return $this|void
     */
    public function execute()
    {
        /** @var ExportRequestBuilderInterface $reqBuilder */
        foreach ($this->reqBuilders as $reqBuilder) {
            foreach ($this->websites as $website) {
                $reqBuilder->build($website);
            }
        }
    }
}
