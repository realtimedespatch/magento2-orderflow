<?php

/** @noinspection PhpUndefinedClassInspection */

namespace RealtimeDespatch\OrderFlow\Cron\Import;

use RealtimeDespatch\OrderFlow\Helper\Import\Shipment as ShipmentImportHelper;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Request\CollectionFactory as RequestCollectionFactory;
use RealtimeDespatch\OrderFlow\Api\RequestProcessorFactoryInterface;

/**
 * Shipment Update Import Cron.
 *
 * Cron Job to Import New Shipment Updates to Magento.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class ShipmentUpdateImport extends ImportCron
{
    const ENTITY_SHIPMENT = 'Shipment';

    /**
     * @var ShipmentImportHelper
     */
    protected $helper;

    /**
     * @param ShipmentImportHelper $helper
     * @param RequestCollectionFactory $requestCollectionFactory
     * @param RequestProcessorFactoryInterface $requestProcessorFactory
     */
    public function __construct(
        ShipmentImportHelper $helper,
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
        return self::ENTITY_SHIPMENT;
    }
}
