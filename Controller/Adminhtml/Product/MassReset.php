<?php

namespace RealtimeDespatch\OrderFlow\Controller\Adminhtml\Product;

use Magento\Framework\Controller\ResultFactory;

class MassReset extends \Magento\Catalog\Controller\Adminhtml\Product
{
    /**
     * Massactions filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\DB\Transaction
     */
    protected $_tx;

    /**
     * @param Context $context
     * @param Builder $productBuilder
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Magento\Framework\DB\Transaction $tx
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->_tx = $tx;
        parent::__construct($context, $productBuilder);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();

        foreach ($collection->getItems() as $product) {
            $product->setOrderflowExportStatus('Pending');
            $this->_tx->addObject($product);
        }

        try {
            $this->_tx->save();
            $this->messageManager->addSuccess(__('A total of %1 product(s) have had their OrderFlow export status reset.', $collectionSize));
        } catch (\Exception $ex) {
            $this->messageManager->addError(__('OrderFlow Export Status reset failed: '.$ex->getMessage()));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setRefererUrl();
    }
}