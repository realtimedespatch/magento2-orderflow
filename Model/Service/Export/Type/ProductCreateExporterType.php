<?php

namespace RealtimeDespatch\OrderFlow\Model\Service\Export\Type;

use \RealtimeDespatch\OrderFlow\Model\Factory\OrderFlow\Service\ProductServiceFactory;

class ProductCreateExporterType extends \RealtimeDespatch\OrderFlow\Model\Service\Export\Type\ExporterType
{
    /* Exporter Type */
    const TYPE = 'Product';

    /**
     * @param \RealtimeDespatch\OrderFlow\Model\Factory\OrderFlow\Service\ProductServiceFactory
     */
    protected $_factory;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @pparam \RealtimeDespatch\OrderFlow\Model\Factory\OrderFlow\Service\ProductService $factory
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        ProductServiceFactory $factory
    ) {
        parent::__construct($config, $logger, $objectManager);
        $this->_factory = $factory;
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
        $tx = $this->_objectManager->create('Magento\Framework\DB\Transaction');
        $export = $this->_createExport($request);
        $exportLines = array();

        $tx->addObject($export);
        $tx->addObject($request);

        foreach ($request->getLines() as $requestLine) {
            $exportLine = $this->_exportLine($export, $request, $requestLine);
            $export->addLine($exportLine);
            $tx->addObject($exportLine);
            $tx->addObject($requestLine);

        }

        $request->setProcessedAt(date('Y-m-d H:i:s'));
        $tx->save();

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
        $service = $this->_factory->getService($request->getScopeId());

        try {
            $service->notifyProductUpdate($sku);
            $requestLine->setResponse((string) $service->getLastResponseBody());

            return $this->_createSuccessExportLine(
                $export,
                $sku,
                $request->getOperation(),
                __('OrderFlow successfully notified that product '.$sku.' is pending export'),
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