<?php

/** @noinspection PhpUndefinedClassInspection */

namespace RealtimeDespatch\OrderFlow\Cron\Import;

use RealtimeDespatch\OrderFlow\Helper\Import\Inventory as InventoryImportHelper;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Request\CollectionFactory as RequestCollectionFactory;
use RealtimeDespatch\OrderFlow\Api\RequestProcessorFactoryInterface;

/**
 * Inventory Update Import Cron.
 *
 * Cron Job to Import New Inventory Updates to Magento.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class InventoryUpdateImport extends ImportCron
{
    const ENTITY_INVENTORY = 'Inventory';

    /**
     * @var InventoryImportHelper
     */
    protected $helper;

    /**
     * @param InventoryImportHelper $helper
     * @param RequestCollectionFactory $requestCollectionFactory
     * @param RequestProcessorFactoryInterface $requestProcessorFactory
     */
    public function __construct(
        InventoryImportHelper $helper,
        RequestCollectionFactory $requestCollectionFactory,
        RequestProcessorFactoryInterface $requestProcessorFactory
    ) {
        $this->helper = $helper;

        parent::__construct(
            $requestCollectionFactory,
            $requestProcessorFactory
        );
    }

    /**
     * @inheritDoc
     */
    protected function getHelper()
    {
        return $this->helper;
    }

    /**
     * @inheritDoc
     */
    protected function getEntityType()
    {
        return self::ENTITY_INVENTORY;
    }
}
