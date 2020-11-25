<?php /** @noinspection PhpDeprecationInspection */

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Product\Edit\Info;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Registry;
use RealtimeDespatch\OrderFlow\Helper\Admin\Info;
use RealtimeDespatch\OrderFlow\Helper\Api;

/**
 * Class OrderFlow
 * @package RealtimeDespatch\OrderFlow\Block\Adminhtml\Product\Edit\Info
 */
class OrderFlow extends Template
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var Http
     */
    protected $_request;

    /**
     * @var Info
     */
    protected $_adminInfoHelper;

    /**
     * @var Api
     */
    protected $_apiHelper;

    /**
     * OrderFlow constructor.
     * @param Context $context
     * @param Registry $registry,
     * @param Http $request
     * @param ManagerInterface $messageManager
     * @param Info $adminInfoHelper
     * @param Api $apiHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Http $request,
        ManagerInterface $messageManager,
        Info $adminInfoHelper,
        Api $apiHelper,
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
     * @return Product
     */
    public function getProduct()
    {
        if ( ! $this->getData('product') instanceof Product) {
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
        $storeId = $this->_request->getParam('store', 0);

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
        $storeId = $this->_request->getParam('store', 0);

        $url  = $this->_apiHelper->getEndpoint($storeId);
        $url .= 'inventory/inventory/referenceDetail.htm?externalReference';
        $url .= urlencode($product->getSku());
        $url .= '&channel='.urlencode($this->_apiHelper->getChannel($storeId));

        return $url;
    }
}
