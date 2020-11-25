<?php

namespace RealtimeDespatch\OrderFlow\Controller\Adminhtml\Product;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Catalog\Controller\Adminhtml\Product;
use Magento\Catalog\Controller\Adminhtml\Product\Builder;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\DB\Transaction;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;

class MassReset extends Product
{
    /**
     * Mass Actions Filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var Transaction
     */
    protected $transaction;

    /**
     * @param Context $context
     * @param Builder $productBuilder
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param Transaction $transaction
     */
    public function __construct(
        Context $context,
        Builder $productBuilder,
        Filter $filter,
        CollectionFactory $collectionFactory,
        Transaction $transaction
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->transaction = $transaction;
        parent::__construct($context, $productBuilder);
    }

    /**
     * @return Redirect
     * @throws LocalizedException
     * @throws LocalizedException
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();

        foreach ($collection->getItems() as $product) {
            /* @var \Magento\Catalog\Model\Product $product */
            /** @noinspection PhpUndefinedMethodInspection */
            $product->setOrderflowExportStatus('Pending');
            $this->transaction->addObject($product);
        }

        try {
            $this->transaction->save();
            $this->messageManager->addSuccessMessage(
                __('A total of %1 product(s) have had their OrderFlow export status reset.', $collectionSize)
            );
        } catch (Exception $ex) {
            $this->messageManager->addErrorMessage(
                __('OrderFlow Export Status reset failed: '.$ex->getMessage())
            );
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setRefererUrl();
    }
}
