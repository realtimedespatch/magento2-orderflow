<?php

namespace RealtimeDespatch\OrderFlow\Model\Service\Import\Type;

 use \RealtimeDespatch\OrderFlow\Helper\Stock as StockHelper;

class InventoryUpdateImporterType extends \RealtimeDespatch\OrderFlow\Model\Service\Import\Type\ImporterType
{
    /* Importer Type */
    const TYPE = 'Inventory';

    /**
     * @param \RealtimeDespatch\OrderFlow\Helper\StockUpdater
     */
    protected $_stockHelper;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \RealtimeDespatch\OrderFlow\Helper\Stock $stockHelper
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        StockHelper $stockHelper
    ) {
        parent::__construct($config, $logger, $objectManager);
        $this->_stockHelper = $stockHelper;
    }

    /**
     * Checks whether the import type is enabled.
     *
     * @api
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->_config->getValue(
            'orderflow_inventory_import/settings/is_enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Returns the import type.
     *
     * @api
     * @return string
     */
    public function getType()
    {
        return self::TYPE;
    }

    /**
     * Imports a request.
     *
     * @api
     * @param \RealtimeDespatch\OrderFlow\Model\Request $request
     *
     * @return mixed
     */
    public function import(\RealtimeDespatch\OrderFlow\Model\Request $request)
    {
        $tx          = $this->_objectManager->create('Magento\Framework\DB\Transaction');
        $import      = $this->_createImport($request);
        $importLines = array();

        $tx->addObject($import);
        $tx->addObject($request);

        foreach ($request->getLines() as $requestLine) {
            $importLine = $this->_importLine($import, $request, $requestLine);
            $importLine->setImport($import);
            $tx->addObject($importLine);
            $tx->addObject($requestLine);
        }

        $request->setProcessedAt(date('Y-m-d H:i:s'));

        $tx->save();
    }

    /**
     * Imports a request line;
     *
     * @api
     * @param \RealtimeDespatch\OrderFlow\Model\Request $request
     *
     * @return mixed
     */
    protected function _importLine($import, $request, $requestLine)
    {
        $seqId = $requestLine->getSequenceId();

        try {
            // Retrieve inventory parameters
            $body = $requestLine->getBody();
            $sku  = (string) $body->sku;
            $unitsReceived = (integer) $body->qty;
            $source = (string) $body->source;
            $lastOrderExported = isset($body->lastOrderExported) ? new \DateTime($body->lastOrderExported) : new \DateTime;

            $reference = implode('_', [$sku, $source]);

            // Check for a duplicate import line
            if ($this->_isDuplicateLine($requestLine->getSequenceId())) {
                return $this->_createDuplicateImportLine(
                    $import,
                    $seqId,
                    $reference,
                    $request->getOperation(),
                    __('Duplicate Inventory Request Ignored.')
                );
            }

            // Check whether this import line has been superseded
            if ($supersedeId = $this->_isSuperseded($seqId, $sku)) {
                return $this->_createSupersededImportLine(
                    $import,
                    $seqId,
                    $reference,
                    $request->getOperation(),
                    sprintf(
                        'Product quantity update to %d discarded as already superseded by inventory record %d',
                        $unitsReceived,
                        $supersedeId
                    )
                );
            }

            // Update the product's inventory
            $this->_stockHelper->updateProductStock($sku, $unitsReceived, $lastOrderExported, $source);

            return $this->_createSuccessImportLine(
                $import,
                $seqId,
                $reference,
                $request->getOperation(),
                __('Product Quantity Successfully Updated to ').$unitsReceived,
                []
            );
        } catch (\Exception $ex) {
            return $this->_createFailureImportLine(
                $import,
                $seqId,
                $reference ?? null,
                $request->getOperation(),
                $ex->getMessage()
            );
        }
    }
}
