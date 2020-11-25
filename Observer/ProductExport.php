<?php

namespace RealtimeDespatch\OrderFlow\Observer;

use Exception;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\DB\Transaction;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class ProductExport implements ObserverInterface
{
    /**
     * @var Transaction
     */
    protected $transaction;

    /**
     * @var ProductRepository
     */
    protected $repository;

    /**
     * ProductExport constructor.
     * @param Transaction $transaction
     * @param ProductRepository $repository
     */
    public function __construct(
        Transaction $transaction,
        ProductRepository $repository
    ) {
        $this->transaction = $transaction;
        $this->repository = $repository;
    }

    /**
     * Updates the status of each product that has been successfully integrated into OrderFlow.
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

        if ( ! $export->isProductExport()) {
            return;
        }

        foreach ($export->getLines() as $exportLine) {
            $this->updateProductExportStatus($exportLine);
        }

        $this->transaction->save();
    }

    /**
     * Updates the export status for a single product from an export line.
     *
     * @param $exportLine
     *
     * @return false|void
     */
    protected function updateProductExportStatus($exportLine)
    {
        try {
            $product = $this->repository->get($exportLine->getReference(), true, 0);

            /** @noinspection PhpUndefinedMethodInspection */
            $product->setOrderflowExportStatus($exportLine->getEntityExportStatus());
            $this->transaction->addObject($product);
        } catch (NoSuchEntityException $ex) {
            return false;
        }
    }
}
