<?php

namespace RealtimeDespatch\OrderFlow\Controller\Adminhtml\Product;

use Magento\Backend\App\Action\Context;

class Export extends \Magento\Backend\App\Action
{
    /**
     * @var \RealtimeDespatch\OrderFlow\Helper\Export\Product
     */
    protected $_exportHelper;

    /**
     * @var \RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface
     */
    protected $_builder;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $_repository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param Context $context
     * @param \RealtimeDespatch\OrderFlow\Helper\Export\Product $helper
     * @param \RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface $builder
     * @param \Magento\Catalog\Model\ProductRepository $repository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        \RealtimeDespatch\OrderFlow\Helper\Export\Product $helper,
        \RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface $builder,
        \Magento\Catalog\Model\ProductRepository $repository,
        \Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        $this->_exportHelper = $helper;
        $this->_builder = $builder;
        $this->_repository = $repository;
        $this->_storeManager = $storeManager;

        parent::__construct($context);
    }

    /**
     * Export Action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        // Check whether product exports are enabled.
        if ( ! $this->_exportHelper->isEnabled()) {
            $this->messageManager->addError(__('Product exports are currently disabled. Please review the OrderFlow module configuration.'));
            return $resultRedirect->setRefererUrl();
        }

        try {
            $product = $this->_getProduct();

            if ( ! $product) {
                return $resultRedirect->setRefererUrl();
            }

            if ($product->getTypeId() !== 'simple') {
                $this->messageManager->addError(__('This product cannot be exported. OrderFlow only supports simple product types.'));
                return $resultRedirect->setRefererUrl();
            }

            $request = $this->_buildRequest($product);
            $export  = $this->_getRequestProcessor()->process($request);

            if ($export->getFailures() || $export->getDuplicates()) {
                $this->messageManager->addError(__('Product '.$product->getSku().' has failed to be queued for export to OrderFlow.'));
            } else {
                $this->messageManager->addSuccess(__('Product '.$product->getSku().' has been queued for export to OrderFlow.'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

        return $resultRedirect->setRefererUrl();
    }

    /**
     * Retrieves a product from the current request
     *
     * @return \Magento\Sales\Api\Data\ProductInterface
     */
    protected function _getProduct()
    {
        $id = $this->getRequest()->getParam('id');

        try {
            $product = $this->_repository->getById($id);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addError(__('Product Not Found.'));
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return false;
        } catch (InputException $e) {
            $this->messageManager->addError(__('Product Not Found.'));
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return false;
        }

        return $product;
    }

    /**
     * Retrieves a product from the current request
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface
     */
    protected function _buildRequest($product)
    {
        $operation = \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface::OP_UPDATE;

        if ( ! $product->getOrderflowExportDate()) {
            $operation = \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface::OP_CREATE;
        }

        $this->_builder->setRequestData(
            \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface::TYPE_EXPORT,
            \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface::ENTITY_PRODUCT,
            $operation
        );

        $this->_builder->setScopeId($this->_getWebsiteId());
        $this->_builder->addRequestLine(json_encode(array('sku' => $product->getSku())));

        return $this->_builder->saveRequest();
    }

    /**
     * Retrieve the request processor instance.
     *
     * @return RealtimeDespatch\OrderFlow\Model\Service\Request\RequestProcessor
     */
    protected function _getRequestProcessor()
    {
        return $this->_objectManager->create('ProductCreateRequestProcessor');
    }

    /**
     * Returns the current website ID.
     *
     * @return integer
     */
    protected function _getWebsiteId()
    {
        return $this->_storeManager->getStore($this->_request->getParam('store'))->getWebsiteId();
    }
}