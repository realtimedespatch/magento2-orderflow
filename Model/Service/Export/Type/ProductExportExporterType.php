<?php

namespace RealtimeDespatch\OrderFlow\Model\Service\Export\Type;

class ProductExportExporterType extends \RealtimeDespatch\OrderFlow\Model\Service\Export\Type\ExporterType
{
    /* Exporter Type */
    const TYPE = 'Product';

    /**
     * @var \Magento\Framework\DB\Transaction
     */
    protected $_tx;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $_productRepository;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @pparam \Magento\Catalog\Model\ProductRepository $productRepository
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Model\ProductRepository $productRepository
    ) {
        parent::__construct($config, $logger, $objectManager);
        $this->_productRepository = $productRepository;
        $this->_tx = $this->_objectManager->create('Magento\Framework\DB\Transaction');
    }

    /**
     * Checks whether the export type is enabled.
     *
     * @api
     * @return boolean
     */
    public function isEnabled($scopeId = null)
    {
        return $this->_config->getValue(
            'orderflow_product_export/settings/is_enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $scopeId
        );
    }

    /**
     * Returns the export type.
     *
     * @api
     * @return string
     */
    public function getType()
    {
        return self::TYPE;
    }

    /**
     * Exports a request.
     *
     * @api
     * @param \RealtimeDespatch\OrderFlow\Model\Request $request
     *
     * @return mixed
     */
    public function export(\RealtimeDespatch\OrderFlow\Model\Request $request)
    {
        $export = $this->_createExport($request);
        $exportLines = array();

        $this->_tx->addObject($export);
        $this->_tx->addObject($request);

        foreach ($request->getLines() as $requestLine) {
            $exportLine = $this->_exportLine($export, $request, $requestLine);
            $export->addLine($exportLine);
            $this->_tx->addObject($requestLine);
            $this->_tx->addObject($exportLine);
        }

        $request->setProcessedAt(date('Y-m-d H:i:s'));
        $this->_tx->save();

        return $export;
    }

    /**
     * Exports a request line;
     *
     * @api
     * @param \RealtimeDespatch\OrderFlow\Model\Request $request
     *
     * @return mixed
     */
    protected function _exportLine($export, $request, $requestLine)
    {
        $body = $requestLine->getBody();
        $sku = (string) $body->sku;

        try {
            $product = $this->_productRepository->get($sku, true, 0);
            $product->setOrderflowExportStatus(__('Exported'));
            $product->setOrderflowExportDate($request->getCreationTime());
            $this->_tx->addObject($product);

            $requestLine->setResponse(__('Product successfully exported.'));

            return $this->_createSuccessExportLine(
                $export,
                $sku,
                $request->getOperation(),
                __('Product successfully exported.'),
                $body
            );
        } catch (\Exception $ex) {
            $requestLine->setResponse($ex->getMessage());

            return $this->_createFailureExportLine(
                $export,
                $sku,
                $request->getOperation(),
                $ex->getMessage()
            );
        }
    }
}