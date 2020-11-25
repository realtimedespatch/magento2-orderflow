<?php

namespace RealtimeDespatch\OrderFlow\Observer;

use Exception;
use Magento\Framework\DB\Transaction;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;

class OrderExport implements ObserverInterface
{
    /**
     * @var Transaction
     */
    protected $transaction;

    /**
     * @var OrderInterface
     */
    protected $repository;

    /**
     * @param Transaction $transaction
     * @param OrderInterface $repository
     */
    public function __construct(
        Transaction $transaction,
        OrderInterface $repository
    ) {
        $this->transaction = $transaction;
        $this->repository = $repository;
    }

    /**
     * Updates the export statuses for a set of order from an export report.
     *
     * @param Observer $observer
     *
     * @return void
     * @throws Exception
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        $export = $observer->getData('export');

        if ( ! $export->isOrderExport()) {
            return;
        }

        foreach ($export->getLines() as $exportLine) {
            $this->updateOrderExportStatus($exportLine);
        }

        $this->transaction->save();
    }

    /**
     * Updates the status of each order that has been successfully integrated into OrderFlow.
     *
     * @param $exportLine
     *
     * @return false|void
     */
    protected function updateOrderExportStatus($exportLine)
    {
        try {
            // Ignore failed cancellations.
            if ($exportLine->isFailure() && $exportLine->isCancellation()) {
                return false;
            }

            $order = $this->repository->loadByIncrementId($exportLine->getReference());

            if ( ! $order->getId()) {
                return false;
            }

            $order->setOrderflowExportStatus($exportLine->getEntityExportStatus(), true, 0);
            $this->transaction->addObject($order);
        } catch (NoSuchEntityException $ex) {
            return false;
        }
    }
}
