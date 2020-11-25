<?php

/** @noinspection PhpUndefinedClassInspection */

namespace RealtimeDespatch\OrderFlow\Model\Service\Export\Type;

use RealtimeDespatch\OrderFlow\Api\Data\ExportInterfaceFactory;
use RealtimeDespatch\OrderFlow\Api\Data\ExportLineInterfaceFactory;

/**
 * Product Update Exporter Type.
 *
 * Processes product update requests sent from Magento to OrderFlow to ensure products are marked as queued.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class ProductUpdateExporterType extends ProductCreateExporterType
{

}
