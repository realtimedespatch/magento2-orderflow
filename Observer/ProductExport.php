<?php

namespace RealtimeDespatch\OrderFlow\Observer;

use Magento\Framework\Event\ObserverInterface;

class ProductExport implements ObserverInterface
{
    /**
     * @var \Magento\Framework\DB\Transaction
     */
    protected $_tx;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $_repository;

    /**
     * ProductExport constructor.
     * @param \Magento\Framework\DB\Transaction $tx
     * @param \Magento\Catalog\Model\ProductRepository $repository
     */
    public function __construct(\Magento\Framework\DB\Transaction $tx,
        \Magento\Catalog\Model\ProductRepository $repository)
    {
        $this->_tx = $tx;
        $this->_repository = $repository;
    }

    /**
     * Updates the export statuses for a set of product from an export report.
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $export = $observer->getData('export');

        if ( ! $export->isProductExport()) {
            return;
        }

        foreach ($export->getLines() as $exportLine) {
            $this->_updateProductExportStatus($exportLine);
        }

        $this->_tx->save();
    }

    /**
     * Updates the export status for a single product from an export line.
     *
     * @param $exportLine
     *
     * @return void
     */
    protected function _updateProductExportStatus($exportLine)
    {
        try {
            $product = $this->_repository->get($exportLine->getReference(), true, \Magento\Store\Model\Store::DEFAULT_STORE_ID);
            $product->setOrderflowExportStatus($exportLine->getEntityExportStatus());
            $this->_tx->addObject($product);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $ex) {
            return false;
        }
    }
}
