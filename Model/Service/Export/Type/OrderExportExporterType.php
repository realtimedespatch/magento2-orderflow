<?php

namespace RealtimeDespatch\OrderFlow\Model\Service\Export\Type;

class OrderExportExporterType extends \RealtimeDespatch\OrderFlow\Model\Service\Export\Type\ExporterType
{
    /* Exporter Type */
    const TYPE = 'Order';

    /**
     * @var \Magento\Framework\DB\Transaction
     */
    protected $_tx;

    /**
     * @var \Magento\Sales\Api\Data\OrderInterface $repository
     */
    protected $_orderRepository;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @pparam \Magento\Sales\Api\Data\OrderInterface $orderRepository
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Sales\Api\Data\OrderInterface $orderRepository
    ) {
        parent::__construct($config, $logger, $objectManager);
        $this->_orderRepository = $orderRepository;
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
        $incrementId = (string) $body->increment_id;

        try {
            $order = $this->_orderRepository->loadByIncrementId($incrementId);
            $order->setOrderflowExportStatus(__('Exported'));
            $order->setOrderflowExportDate($request->getCreatedAt());
            $this->_tx->addObject($order);

            $requestLine->setResponse(__('Order successfully exported.'));

            return $this->_createSuccessExportLine(
                $export,
                $incrementId,
                $request->getOperation(),
                __('Order successfully exported.'),
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