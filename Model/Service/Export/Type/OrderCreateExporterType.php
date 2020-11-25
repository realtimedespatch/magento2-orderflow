<?php

/** @noinspection PhpUndefinedClassInspection */

namespace RealtimeDespatch\OrderFlow\Model\Service\Export\Type;

use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DB\Transaction;
use Magento\Store\Model\ScopeInterface;
use RealtimeDespatch\OrderFlow\Api\Data\ExportInterface;
use RealtimeDespatch\OrderFlow\Api\Data\ExportInterfaceFactory;
use RealtimeDespatch\OrderFlow\Api\Data\ExportLineInterfaceFactory;
use \RealtimeDespatch\OrderFlow\Model\Factory\OrderFlow\Service\OrderServiceFactory;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use Psr\Log\LoggerInterface;

/**
 * Product Create Exporter Type.
 *
 * Processes order create requests sent from Magento to OrderFlow to ensure orders are marked as queued.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class OrderCreateExporterType extends ExporterType
{
    /* Exporter Type */
    const TYPE = 'Order';

    /**
     * @param OrderServiceFactory
     */
    protected $orderServiceFactory;

    /**
     * @param ScopeConfigInterface $config
     * @param LoggerInterface $logger
     * @param ExportInterfaceFactory $exportFactory
     * @param ExportLineInterfaceFactory $exportLineFactory
     * @param Transaction $transaction
     * @param OrderServiceFactory $orderServiceFactory
     */
    public function __construct(
        ScopeConfigInterface $config,
        LoggerInterface $logger,
        ExportInterfaceFactory $exportFactory,
        ExportLineInterfaceFactory $exportLineFactory,
        Transaction $transaction,
        OrderServiceFactory $orderServiceFactory
    ) {
        $this->orderServiceFactory = $orderServiceFactory;

        parent::__construct(
            $config,
            $logger,
            $exportFactory,
            $exportLineFactory,
            $transaction
        );
    }

    /**
     * @inheritDoc
     */
    public function isEnabled($scopeId = null)
    {
        return $this->config->getValue(
            'orderflow_order_export/settings/is_enabled',
            ScopeInterface::SCOPE_WEBSITE,
            $scopeId
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
    protected function exportLine(
        ExportInterface $export,
        RequestInterface $request,
        $requestLine
    ) {
        $body = $requestLine->getBody();
        $incrementId = (string) $body->increment_id;
        $entityId = (int) $body->entity_id;
        $service = $this->orderServiceFactory->getService($request->getScopeId());

        try {
            $service->notifyOrderCreation($entityId, $incrementId);

            $responseBody = (string) $service->getLastResponseBody();
            $requestLine->setResponse($responseBody);

            return $this->createSuccessExportLine(
                $export,
                $incrementId,
                $request->getOperation(),
                __('OrderFlow successfully notified that order '.$incrementId.' is pending export'),
                $body
            );
        } catch (Exception $ex) {
            $requestLine->setResponse($ex->getMessage());

            return $this->createFailureExportLine(
                $export,
                $incrementId,
                $request->getOperation(),
                $ex->getMessage()
            );
        }
    }
}
