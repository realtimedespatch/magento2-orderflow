<?php

namespace RealtimeDespatch\OrderFlow\Observer;

use Magento\Framework\Event\ObserverInterface;

class OrderExport implements ObserverInterface
{
    /**
     * @var \Magento\Framework\DB\Transaction
     */
    protected $_tx;

    /**
     * @var \Magento\Sales\Api\Data\OrderInterface
     */
    protected $_repository;

    /**
     * OrderExport constructor.
     * @param \Magento\Framework\DB\Transaction $tx
     * @param \Magento\Sales\Api\Data\OrderInterface $repository
     */
    public function __construct(\Magento\Framework\DB\Transaction $tx,
        \Magento\Sales\Api\Data\OrderInterface $repository)
    {
        $this->_tx = $tx;
        $this->_repository = $repository;
    }

    /**
     * Updates the export statuses for a set of order from an export report.
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $export = $observer->getData('export');

        if ( ! $export->isOrderExport()) {
            return;
        }

        foreach ($export->getLines() as $exportLine) {
            $this->_updateOrderExportStatus($exportLine);
        }

        $this->_tx->save();
    }

    /**
     * Updates the export status for a single order from an export line.
     *
     * @param $exportLine
     *
     * @return void
     */
    protected function _updateOrderExportStatus($exportLine)
    {
        try {
            // Ignore failed cancellations.
            if ($exportLine->isFailure() && $exportLine->isCancellation()) {
                return false;
            }

            $order = $this->_repository->loadByIncrementId($exportLine->getReference());

            if ( ! $order->getId()) {
                return false;
            }

            $order->setOrderflowExportStatus($exportLine->getEntityExportStatus(), true, 0);
            $this->_tx->addObject($order);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $ex) {
            return false;
        }
    }
}