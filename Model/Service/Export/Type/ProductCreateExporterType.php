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
use \RealtimeDespatch\OrderFlow\Model\Factory\OrderFlow\Service\ProductServiceFactory;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use Psr\Log\LoggerInterface;

/**
 * Product Create Exporter Type.
 *
 * Processes product create requests sent from Magento to OrderFlow to ensure products are marked as queued.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class ProductCreateExporterType extends ExporterType
{
    /* Exporter Type */
    const TYPE = 'Product';

    /**
     * @param ProductServiceFactory
     */
    protected $productServiceFactory;

    /**
     * @param ScopeConfigInterface $config
     * @param LoggerInterface $logger
     * @param ExportInterfaceFactory $exportFactory
     * @param ExportLineInterfaceFactory $exportLineFactory
     * @param Transaction $transaction
     * @param ProductServiceFactory $productServiceFactory
     */
    public function __construct(
        ScopeConfigInterface $config,
        LoggerInterface $logger,
        ExportInterfaceFactory $exportFactory,
        ExportLineInterfaceFactory $exportLineFactory,
        Transaction $transaction,
        ProductServiceFactory $productServiceFactory
    ) {
        $this->productServiceFactory = $productServiceFactory;

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
            'orderflow_product_export/settings/is_enabled',
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
        $sku = (string) $body->sku;
        $service = $this->productServiceFactory->getService($request->getScopeId());

        try {
            $service->notifyProductUpdate($sku);

            $responseBody = (string) $service->getLastResponseBody();

            $requestLine->setResponse($responseBody);

            return $this->createSuccessExportLine(
                $export,
                $sku,
                $request->getOperation(),
                __('OrderFlow successfully notified that product '.$sku.' is pending export'),
                $body
            );
        } catch (Exception $ex) {
            $requestLine->setResponse($ex->getMessage());

            return $this->createFailureExportLine(
                $export,
                $sku,
                $request->getOperation(),
                $ex->getMessage()
            );
        }
    }
}
