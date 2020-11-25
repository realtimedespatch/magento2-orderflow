<?php

namespace RealtimeDespatch\OrderFlow\Controller\Adminhtml\Product;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Type;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Store\Model\StoreManagerInterface;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface;
use RealtimeDespatch\OrderFlow\Helper\Export\Product as ProductExportHelper;
use RealtimeDespatch\OrderFlow\Model\Service\Request\RequestProcessor;

/**
 * Product Export Controller.
 *
 * Handles the request to queue a product for export to OrderFlow.
 */
class Export extends Action
{
    /**
     * @var ProductExportHelper
     */
    protected $helper;

    /**
     * @var RequestProcessor
     */
    protected $requestProcessor;

    /**
     * @var RequestBuilderInterface
     */
    protected $requestBuilder;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Context $context
     * @param ProductExportHelper $helper
     * @param RequestProcessor $requestProcessor
     * @param RequestBuilderInterface $requestBuilder
     * @param ProductRepositoryInterface $productRepository
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        ProductExportHelper $helper,
        RequestProcessor $requestProcessor,
        RequestBuilderInterface $requestBuilder,
        ProductRepositoryInterface $productRepository,
        StoreManagerInterface $storeManager
    ) {
        $this->helper = $helper;
        $this->requestProcessor = $requestProcessor;
        $this->requestBuilder = $requestBuilder;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;

        parent::__construct($context);
    }

    /**
     * Execute.
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if (! $this->helper->isEnabled()) {
            $this->messageManager->addErrorMessage(
                __('Product exports are disabled. Please review the OrderFlow module configuration.')
            );

            return $resultRedirect->setRefererUrl();
        }

        try {
            $product = $this->getProduct();

            if (! $product) {
                return $resultRedirect->setRefererUrl();
            }

            if ($product->getTypeId() !== Type::TYPE_SIMPLE) {
                $this->messageManager->addErrorMessage(
                    __('Only simple products can be exported.')
                );

                return $resultRedirect->setRefererUrl();
            }

            $request = $this->buildRequest($product);
            $export  = $this->requestProcessor->process($request);

            if ($export->getFailures() || $export->getDuplicates()) {
                $this->messageManager->addErrorMessage(__('Product export failed.'));
            } else {
                $this->messageManager->addSuccessMessage(__('Product successfully queued for export.'));
            }
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setRefererUrl();
    }

    /**
     * Product Getter.
     *
     * @return ProductInterface|boolean
     */
    protected function getProduct()
    {
        $productId = $this->getRequest()->getParam('id');

        try {
            $product = $this->productRepository->getById($productId);
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Product with ID: '.$productId.' cannot be retrieved.')
            );
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return false;
        }

        return $product;
    }

    /**
     * Build Export Request.
     *
     * @param ProductInterface $product
     * @return RequestInterface
     * @throws NoSuchEntityException
     */
    protected function buildRequest(ProductInterface $product)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $exportDate = $product->getOrderflowExportDate();
        $operation = $exportDate ? RequestInterface::OP_UPDATE : RequestInterface::OP_CREATE;

        $this->requestBuilder->setRequestData(
            RequestInterface::TYPE_EXPORT,
            RequestInterface::ENTITY_PRODUCT,
            $operation
        );

        $this->requestBuilder->setScopeId($this->getWebsiteId());
        $this->requestBuilder->addRequestLine(json_encode(['sku' => $product->getSku()]));

        return $this->requestBuilder->saveRequest();
    }

    /**
     * Website ID Getter.
     *
     * @return integer
     * @throws NoSuchEntityException
     */
    protected function getWebsiteId()
    {
        return $this->storeManager->getStore($this->_request->getParam('store'))->getWebsiteId();
    }
}
