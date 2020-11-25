<?php

/** @noinspection PhpUndefinedClassInspection */

namespace RealtimeDespatch\OrderFlow\Model\Service\Export\Type;

use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DB\Transaction;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\OrderFactory;
use Magento\Store\Model\ScopeInterface;
use RealtimeDespatch\OrderFlow\Api\Data\ExportInterface;
use RealtimeDespatch\OrderFlow\Api\Data\ExportInterfaceFactory;
use RealtimeDespatch\OrderFlow\Api\Data\ExportLineInterfaceFactory;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use Psr\Log\LoggerInterface;

/**
 * Order Export Exporter Type.
 *
 * Processes order export requests received from OrderFlow to ensure orders are marked as exported.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class OrderExportExporterType extends ExporterType
{
    /* Exporter Type */
    const TYPE = 'Order';

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @param ScopeConfigInterface $config
     * @param LoggerInterface $logger
     * @param ExportInterfaceFactory $exportFactory
     * @param ExportLineInterfaceFactory $exportLineFactory
     * @param Transaction $transaction
     * @param OrderFactory $orderFactory
     */
    public function __construct(
        ScopeConfigInterface $config,
        LoggerInterface $logger,
        ExportInterfaceFactory $exportFactory,
        ExportLineInterfaceFactory $exportLineFactory,
        Transaction $transaction,
        OrderFactory $orderFactory
    ) {
        $this->orderFactory = $orderFactory;

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

        try {
            $order = $this->orderFactory->create()->loadByIncrementId($incrementId);

            if (! $order->getId()) {
                throw new LocalizedException(__('Order #'.$incrementId.' does not exist.'));
            }

            /** @noinspection PhpUndefinedMethodInspection */
            $order->setOrderflowExportStatus(__('Exported'));

            /** @noinspection PhpUndefinedMethodInspection */
            $order->setOrderflowExportDate($request->getCreationTime());
            $this->transaction->addObject($order);

            $requestLine->setResponse(__('Order successfully exported.'));

            return $this->createSuccessExportLine(
                $export,
                $incrementId,
                $request->getOperation(),
                __('Order successfully exported.'),
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
