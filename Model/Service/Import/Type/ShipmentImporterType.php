<?php

namespace RealtimeDespatch\OrderFlow\Model\Service\Import\Type;

use \RealtimeDespatch\OrderFlow\Model\Service\ShipmentService as ShipmentService;

class ShipmentImporterType extends \RealtimeDespatch\OrderFlow\Model\Service\Import\Type\ImporterType
{
    /* Importer Type */
    const TYPE = 'Shipment';

    /**
     * @param \RealtimeDespatch\OrderFlow\Service\ShipmentService
     */
    protected $_shipper;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \RealtimeDespatch\OrderFlow\Service\ShipmentService $shipper
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        ShipmentService $shipper
    ) {
        parent::__construct($config, $logger, $objectManager);
        $this->_shipper = $shipper;
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
            'orderflow_shipment_import/settings/is_enabled',
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
     * Imports an orderflow request.
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

        foreach ($request->getLines() as $requestLine) {
            $importLine = $this->_importLine($import, $request, $requestLine);
            $importLine->setImport($import);
            $tx->addObject($importLine);
        }

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
            $body = $requestLine->getBody();
            $incrementId = (string) $body->orderIncrementId;

            // Check for a duplicate import line
            if ($this->_isDuplicateLine($requestLine->getSequenceId())) {
                return $this->_createDuplicateImportLine(
                    $import,
                    $seqId,
                    $incrementId,
                    $request->getOperation(),
                    __('Duplicate Shipment Line Ignored.')
                );
            }

            // Check whether this import line has been superseded
            if ($supersedeId = $this->_isSuperseded($seqId, $incrementId)) {
                return $this->_createSupersededImportLine(
                    $import,
                    $seqId,
                    $incrementId,
                    $request->getOperation(),
                    sprintf(
                        'Shipment superseded by Request with ID: %d',
                        $supersedeId
                    )
                );
            }

            // Create the shipment.
            $shipment = $this->_shipper->createShipments($body);

            return $this->_createSuccessImportLine(
                $import,
                $seqId,
                $incrementId,
                $request->getOperation(),
                __('Order '.$incrementId.' successfully shipped.'),
                $shipment
            );
        } catch (\Exception $ex) {
            return $this->_createFailureImportLine(
                $import,
                $seqId,
                $incrementId,
                $request->getOperation(),
                'Order '.$incrementId.' not shipped - '.$ex->getMessage()
            );
        }
    }
}