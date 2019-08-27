<?php

namespace RealtimeDespatch\OrderFlow\Model\Service\Export\Type;

use \RealtimeDespatch\OrderFlow\Model\Factory\OrderFlow\Service\OrderServiceFactory;

class OrderCreateExporterType extends \RealtimeDespatch\OrderFlow\Model\Service\Export\Type\ExporterType
{
    /* Exporter Type */
    const TYPE = 'Order';

    /**
     * @param \RealtimeDespatch\OrderFlow\Model\Factory\OrderFlow\Service\OrderServiceFactory
     */
    protected $_factory;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @pparam \RealtimeDespatch\OrderFlow\Model\Factory\OrderFlow\Service\OrderService $factory
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        OrderServiceFactory $factory
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
            'orderflow_order_export/settings/is_enabled',
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
            $tx->addObject($requestLine);
            $tx->addObject($exportLine);
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
        $incrementId = (string) $body->increment_id;
        $entityId = (int) $body->entity_id;
        $service = $this->_factory->getService($request->getScopeId());

        try {
            $service->notifyOrderCreation($incrementId, $entityId);
            $requestLine->setResponse((string) $service->getLastResponseBody());

            return $this->_createSuccessExportLine(
                $export,
                $incrementId,
                $request->getOperation(),
                __('OrderFlow successfully notified that order '.$incrementId.' is pending export'),
                $body
            );
        } catch (\Exception $ex) {
            $requestLine->setResponse($ex->getMessage());

            return $this->_createFailureExportLine(
                $export,
                $incrementId,
                $request->getOperation(),
                $ex->getMessage()
            );
        }
    }
}