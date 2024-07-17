<?php

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Product\Edit\Info;

use Magento\Framework\Registry;

/**
 * Class OrderFlow
 * @package RealtimeDespatch\OrderFlow\Block\Adminhtml\Product\Edit\Info
 */
class OrderFlow extends \Magento\Backend\Block\Template
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * @var \RealtimeDespatch\OrderFlow\Helper\Admin\Info
     */
    protected $_adminInfoHelper;

    /**
     * @var \RealtimeDespatch\OrderFlow\Helper\Api
     */
    protected $_apiHelper;

    /**
     * OrderFlow constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param Registry $registry,
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \RealtimeDespatch\OrderFlow\Helper\Admin\Info $adminInfoHelper
     * @param \RealtimeDespatch\OrderFlow\Helper\Api $apiHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        Registry $registry,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \RealtimeDespatch\OrderFlow\Helper\Admin\Info $adminInfoHelper,
        \RealtimeDespatch\OrderFlow\Helper\Api $apiHelper,
        array $data = [])
    {
        $this->_messageManager = $messageManager;
        $this->_request = $request;
        $this->_coreRegistry = $registry;
        $this->_adminInfoHelper = $adminInfoHelper;
        $this->_apiHelper = $apiHelper;
        parent::__construct($context, $data);
    }

    /**
     * Checks whether the admin info block is enabled.
     *
     * @return bool
     */
    public function canDisplayAdminInfo()
    {
        return $this->_adminInfoHelper->isEnabled();
    }

    /**
     * Retrieve product object from object if not from registry
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        if ( ! $this->getData('product') instanceof \Magento\Catalog\Model\Product) {
            $this->setData('product', $this->_coreRegistry->registry('product'));
        }
        return $this->getData('product');
    }

    /**
     * Returns the OrderFlow URL for the product.
     *
     * @return string
     */
    public function getProductUrl()
    {
        $product = $this->getProduct();
        if (!$product->getSku()) {
            return '';
        }
        $storeId = $this->_request->getParam('store', \Magento\Store\Model\Store::DEFAULT_STORE_ID);

        $url  = $this->_apiHelper->getEndpoint($storeId);
        $url .= 'inventory/product/referenceDetail.htm?externalReference';
        $url .= urlencode($product->getSku());
        $url .= '&channel='.urlencode($this->_apiHelper->getChannel($storeId));

        return $url;
    }

    /**
     * Returns the OrderFlow URL for the inventory.
     *
     * @return string
     */
    public function getInventoryUrl()
    {
        $product = $this->getProduct();
        if (!$product->getSku()) {
            return '';
        }
        $storeId = $this->_request->getParam('store', \Magento\Store\Model\Store::DEFAULT_STORE_ID);

        $url  = $this->_apiHelper->getEndpoint($storeId);
        $url .= 'inventory/inventory/referenceDetail.htm?externalReference';
        $url .= urlencode($product->getSku());
        $url .= '&channel='.urlencode($this->_apiHelper->getChannel($storeId));

        return $url;
    }
}
