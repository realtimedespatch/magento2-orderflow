<?php

/** @noinspection PhpUndefinedClassInspection */

namespace RealtimeDespatch\OrderFlow\Model\Service\Export\Type;

use Exception;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DB\Transaction;
use Magento\Store\Model\ScopeInterface;
use RealtimeDespatch\OrderFlow\Api\Data\ExportInterface;
use RealtimeDespatch\OrderFlow\Api\Data\ExportInterfaceFactory;
use RealtimeDespatch\OrderFlow\Api\Data\ExportLineInterfaceFactory;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use Psr\Log\LoggerInterface;

/**
 * Product Export Exporter Type.
 *
 * Processes product export requests received from OrderFlow to ensure products are marked as exported.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class ProductExportExporterType extends ExporterType
{
    /* Exporter Type */
    const TYPE = 'Product';

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @param ScopeConfigInterface $config
     * @param LoggerInterface $logger
     * @param ExportInterfaceFactory $exportFactory
     * @param ExportLineInterfaceFactory $exportLineFactory
     * @param Transaction $transaction
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        ScopeConfigInterface $config,
        LoggerInterface $logger,
        ExportInterfaceFactory $exportFactory,
        ExportLineInterfaceFactory $exportLineFactory,
        Transaction $transaction,
        ProductRepositoryInterface $productRepository
    ) {
        $this->productRepository = $productRepository;

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

        try {
            /* @var Product $product */
            $product = $this->productRepository->get($sku, true, 0);

            /** @noinspection PhpUndefinedMethodInspection */
            $product->setOrderflowExportStatus(__('Exported'));

            /** @noinspection PhpUndefinedMethodInspection */
            $product->setOrderflowExportDate($request->getCreationTime());

            $this->transaction->addObject($product);

            $requestLine->setResponse(__('Product successfully exported.'));

            return $this->createSuccessExportLine(
                $export,
                $sku,
                $request->getOperation(),
                __('Product successfully exported.'),
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
