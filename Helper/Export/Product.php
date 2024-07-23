<?php

namespace RealtimeDespatch\OrderFlow\Helper\Export;

/**
 * Product Export Helper.
 */
class Product extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Store\Model\ResourceModel\Website\CollectionFactory
     */
    protected $_websiteFactory;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $_productRepository;

    /**
     * @var \RealtimeDespatch\OrderFlow\Helper\Api
     */
    protected $_apiHelper;

    /**
     * @var array
     */
    protected $_configToWebsiteMap;

    /**
     * @var array
     */
    protected $_productExportEnabledWebsiteIds;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productFactory
     * @param \Magento\Store\Model\ResourceModel\Website\CollectionFactory $websiteFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \RealtimeDespatch\OrderFlow\Helper\Api $apiHelper
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productFactory,
        \Magento\Store\Model\ResourceModel\Website\CollectionFactory $websiteFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \RealtimeDespatch\OrderFlow\Helper\Api $apiHelper
    )
    {
        $this->_productFactory = $productFactory;
        $this->_websiteFactory = $websiteFactory;
        $this->_productRepository = $productRepository;
        $this->_apiHelper = $apiHelper;
        parent::__construct($context);
    }

    /**
     * Checks whether the export process is enabled.
     *
     * @param integer|null $scopeId
     *
     * @return boolean
     */
    public function isEnabled($scopeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            'orderflow_product_export/settings/is_enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $scopeId
        );
    }

    /**
     * Returns the maximum batch size for processing.
     *
     * @param integer|null $scopeId
     *
     * @return boolean
     */
    public function getBatchSize($scopeId = null)
    {
        return (integer) $this->scopeConfig->getValue(
            'orderflow_product_export/settings/batch_size',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $scopeId
        );
    }

    /**
     * Returns the default store ID for a website.
     *
     * @param integer|null $scopeId
     *
     * @return boolean
     */
    public function getStoreId($scopeId = null)
    {
        return (integer) $this->scopeConfig->getValue(
            'orderflow_product_export/settings/store_id',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $scopeId
        );
    }

    /**
     * Build a key for the connected OrderFlow instance consisting of endpoint|organisation|channel
     *
     * @param $websiteId
     * @return string
     */
    protected function _getEndpointKey($websiteId)
    {
        return implode('|', [
            $this->_apiHelper->getEndpoint($websiteId),
            $this->_apiHelper->getOrganisation($websiteId),
            $this->_apiHelper->getChannel($websiteId)
        ]);
    }

    protected function _getConfigToWebsiteMap()
    {
        if (NULL === $this->_configToWebsiteMap) {
            $websiteTree = [];
            foreach ($this->getProductExportEnabledWebsiteIds() as $websiteId) {
                /**
                 * Here we'll build a multi-dimensional array of website IDs which have the same
                 * API configuration - i.e. they are connected to the same OrderFlow instance, organisation
                 * and channel
                 *
                 * [
                 *     "https://txnlimitedsandbox.orderflow-wms.co.uk/web/|txn|mal_m2" => ["5","7"],
                 *     "https://txnlimitedtest.orderflow-wms.co.uk/web/|txn|mal_m2" => ["1","2"]
                 * ]
                 */
                $key = $this->_getEndpointKey($websiteId);
                $this->_configToWebsiteMap[$key] ??= [];
                $this->_configToWebsiteMap[$key][] = $websiteId;
            }
        }

        return $this->_configToWebsiteMap;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @param \Magento\Store\Model\Website $website
     * @return void
     */
    protected function applyWebsiteFilterToCollection(
        \Magento\Catalog\Model\ResourceModel\Product\Collection $collection,
        \Magento\Store\Model\Website $website
    ) {
        $configMap = $this->_getConfigToWebsiteMap();
        $endpointKey = $this->_getEndpointKey($website->getId());
        // we only want to return an actual collection of products for the _first_ website
        // that the product appears in (i.e. index 0 in the array). for subsequent websites,
        // we add a filter that will not match any products.
        // "first" here depends on the sort order of the website collection, defined by sort_order and name asc
        if (0 === array_search($website->getId(), $configMap[$endpointKey])) {
            $collection->addWebsiteFilter($configMap[$endpointKey]);
        } else {
            $collection->addWebsiteFilter([999999999]);
        }
    }

    /**
     * Returns a collection of creatable products.
     *
     * @param \Magento\Store\Model\Website $website
     *
     * @return array
     */
    public function getCreateableProducts($website)
    {
        /*
         * To avoid duplicate product export requests where a product is allocated to more than
         * one website, we'll only return the product for the "first" website that it appears in.
         * We create the collection as usual first, then apply a filter to the collection
         */
        $products =  $this->_productFactory->create()
            ->addAttributeToFilter('type_id', ['eq' => 'simple'])
            ->addAttributeToFilter('orderflow_export_date', ['null' => true])
            ->addAttributeToFilter([
                ['attribute' => 'orderflow_export_status', 'null' => true],
                ['attribute' => 'orderflow_export_status', array('neq' => ['Queued'])],
            ],
            '',
            'left')
            ->setStore($this->getStoreId($website->getId()))
            ->setPage(1, $this->getBatchSize($website->getId()));

        $this->applyWebsiteFilterToCollection($products, $website);

        return $products;
    }

    /**
     * Returns a collection of updateable products.
     *
     * @param \Magento\Store\Model\Website $website
     *
     * @return array
     */
    public function getUpdateableProducts($website)
    {
        $products =  $this->_productFactory->create()
            ->addAttributeToFilter('type_id', ['eq' => 'simple'])
            ->addAttributeToFilter('orderflow_export_date', ['notnull' => true])
            ->addFieldToFilter('orderflow_export_status', ['eq' => 'Pending'])
            ->setStore($this->getStoreId($website->getId()))
            ->setPage(1, $this->getBatchSize($website->getId()));

        $this->applyWebsiteFilterToCollection($products, $website);

        return $products;
    }

    /**
     * @return array
     */
    public function getProductExportEnabledWebsiteIds()
    {
        if ($this->_productExportEnabledWebsiteIds === NULL) {
            $this->_productExportEnabledWebsiteIds = [];

            $websites = $this->_websiteFactory->create();
            foreach ($websites as $website) {
                if ($website->getId() == \Magento\Store\Model\Store::DEFAULT_STORE_ID) {
                    continue;
                }
                if ($this->isEnabled($website->getId())) {
                    $this->_productExportEnabledWebsiteIds[] = $website->getId();
                }
            }
        }

        return $this->_productExportEnabledWebsiteIds;
    }

    /**
     * @param string|\Magento\Catalog\Api\Data\ProductInterface $product
     * @return bool
     */
    public function isProductExportEnabledForProductWebsites($product)
    {
        if (!$product instanceof \Magento\Catalog\Api\Data\ProductInterface) {
            $product = $this->_productRepository
                ->get($product, false, \Magento\Store\Model\Store::DEFAULT_STORE_ID);
        }
        $enabledWebsites = $this->getProductExportEnabledWebsiteIds();
        $productWebsites = $product->getWebsiteIds();

        return (bool) array_intersect($enabledWebsites, $productWebsites);
    }
}
