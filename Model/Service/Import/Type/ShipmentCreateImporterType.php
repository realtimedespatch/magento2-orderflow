<?php

/** @noinspection PhpUndefinedClassInspection */

namespace RealtimeDespatch\OrderFlow\Model\Service\Import\Type;

use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DB\Transaction;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;
use RealtimeDespatch\OrderFlow\Api\Data\ImportInterface;
use RealtimeDespatch\OrderFlow\Api\Data\ImportInterfaceFactory;
use RealtimeDespatch\OrderFlow\Api\Data\ImportLineInterfaceFactory;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\ImportLine\CollectionFactory as ImportLineCollectionFactory;
use \RealtimeDespatch\OrderFlow\Model\Service\ShipmentService as ShipmentService;

/**
 * Shipment Create Importer Type.
 *
 * Processes queued shipment creation requests.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class ShipmentCreateImporterType extends ImporterType
{
    /* Importer Type */
    const TYPE = 'Shipment';

    /**
     * @param ShipmentService
     */
    protected $shipper;

    /**
     * @param ScopeConfigInterface $config
     * @param LoggerInterface $logger
     * @param ImportInterfaceFactory $importFactory
     * @param ImportLineInterfaceFactory $importLineFactory
     * @param ImportLineCollectionFactory $importLineCollectionFactory
     * @param Transaction $transaction
     * @param ShipmentService $shipper
     */
    public function __construct(
        ScopeConfigInterface $config,
        LoggerInterface $logger,
        ImportInterfaceFactory $importFactory,
        ImportLineInterfaceFactory $importLineFactory,
        ImportLineCollectionFactory $importLineCollectionFactory,
        Transaction $transaction,
        ShipmentService $shipper
    ) {
        $this->shipper = $shipper;

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
        $incrementId = (string) $body->orderIncrementId;

        try {
            if ($this->isDuplicateLine($requestLine->getSequenceId())) {
                return $this->createDuplicateImportLine(
                    $import,
                    $seqId,
                    $incrementId,
                    $request->getOperation(),
                    __('Duplicate Shipment Line Ignored.')
                );
            }

            if ($supersedeId = $this->isSuperseded($seqId, $incrementId)) {
                return $this->createSupersededImportLine(
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

            $shipment = $this->shipper->createShipment($body);

            return $this->createSuccessImportLine(
                $import,
                $seqId,
                $incrementId,
                $request->getOperation(),
                __('Order '.$incrementId.' successfully shipped.'),
                $shipment->getData()
            );
        } catch (Exception $ex) {
            return $this->createFailureImportLine(
                $import,
                $seqId,
                $incrementId,
                $request->getOperation(),
                'Order '.$incrementId.' not shipped - '.$ex->getMessage()
            );
        }
    }
}
