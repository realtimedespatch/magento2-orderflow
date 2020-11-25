<?php

/** @noinspection PhpUndefinedClassInspection */

namespace RealtimeDespatch\OrderFlow\Model\Service\Import\Type;

use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DB\Transaction;
use Magento\Store\Model\ScopeInterface;
use RealtimeDespatch\OrderFlow\Api\Data\ImportInterface;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use RealtimeDespatch\OrderFlow\Helper\Stock as StockHelper;
use Psr\Log\LoggerInterface;
use RealtimeDespatch\OrderFlow\Api\Data\ImportInterfaceFactory;
use RealtimeDespatch\OrderFlow\Api\Data\ImportLineInterfaceFactory;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\ImportLine\CollectionFactory as ImportLineCollectionFactory;

/**
 * Inventory Updated Importer Type.
 *
 * Processes queued inventory update requests.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class InventoryUpdateImporterType extends ImporterType
{
    /* Importer Type */
    const TYPE = 'Inventory';

    /**
     * @var StockHelper
     */
    protected $stockHelper;

    /**
     * @param ScopeConfigInterface $config
     * @param LoggerInterface $logger
     * @param ImportInterfaceFactory $importFactory
     * @param ImportLineInterfaceFactory $importLineFactory
     * @param ImportLineCollectionFactory $importLineCollectionFactory
     * @param StockHelper $stockHelper
     * @param Transaction $transaction
     */
    public function __construct(
        ScopeConfigInterface $config,
        LoggerInterface $logger,
        ImportInterfaceFactory $importFactory,
        ImportLineInterfaceFactory $importLineFactory,
        ImportLineCollectionFactory $importLineCollectionFactory,
        StockHelper $stockHelper,
        Transaction $transaction
    ) {
        $this->stockHelper = $stockHelper;

        parent::__construct(
            $config,
            $logger,
            $importFactory,
            $importLineFactory,
            $importLineCollectionFactory,
            $transaction
        );
    }

    /**
     * @inheritDoc
     */
    public function isEnabled()
    {
        return $this->config->getValue(
            'orderflow_inventory_import/settings/is_enabled',
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return self::TYPE;
    }

    /**
     * @inheritDoc
     */
    protected function importLine(
        ImportInterface $import,
        RequestInterface $request,
        $requestLine
    ) {
        $seqId = $requestLine->getSequenceId();
        $body = $requestLine->getBody();

        try {
            $sku  = (string) $body->sku;
            $unitsReceived = (integer) $body->qty;

            if ($this->isDuplicateLine($requestLine->getSequenceId())) {
                return $this->createDuplicateImportLine(
                    $import,
                    $seqId,
                    $sku,
                    $request->getOperation(),
                    __('Duplicate Inventory Request Ignored.')
                );
            }

            if ($supersedeId = $this->isSuperseded($seqId, $sku)) {
                return $this->createSupersededImportLine(
                    $import,
                    $seqId,
                    $sku,
                    $request->getOperation(),
                    sprintf(
                        'Product quantity update to %d discarded as already superseded by inventory record %d',
                        $unitsReceived,
                        $supersedeId
                    )
                );
            }

            $inventory = $this->stockHelper->updateProductStock($sku, $unitsReceived);

            return $this->createSuccessImportLine(
                $import,
                $seqId,
                $sku,
                $request->getOperation(),
                __('Product Quantity Successfully Updated to ').$inventory->unitsCalculated,
                $inventory
            );
        } catch (Exception $ex) {
            return $this->createFailureImportLine(
                $import,
                $seqId,
                $sku,
                $request->getOperation(),
                $ex->getMessage()
            );
        }
    }
}
