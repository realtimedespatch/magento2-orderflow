<?php

namespace RealtimeDespatch\OrderFlow\Helper\Export;

/**
 * Product Export Helper.
 */
class Product extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\ProductFactory $productFactory
    )
    {
        $this->_productFactory = $productFactory;
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
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
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
     * Returns a collection of creatable products.
     *
     * @param \Magento\Store\Model\Website $website
     *
     * @return array
     */
    public function getCreateableProducts($website)
    {
        $products =  $this->_productFactory
            ->create()
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('type_id', ['eq' => 'simple'])
            ->addAttributeToFilter([
                ['attribute' => 'orderflow_export_date', 'null' => true],
                ['attribute' => 'orderflow_export_date', 'lt' => new \Zend_Db_Expr('updated_at')],
            ],
            '',
            'left')
            ->addAttributeToFilter([
                ['attribute' => 'orderflow_export_status', 'null' => true],
                ['attribute' => 'orderflow_export_status', array('neq' => ['Queued'])],
            ],
            '',
            'left')
            ->setStore($this->getStoreId($website->getId()))
            ->setPage(1, $this->getBatchSize($website->getId()));

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
        $products =  $this->_productFactory
            ->create()
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('type_id', ['eq' => 'simple'])
            ->addAttributeToFilter('orderflow_export_date', ['notnull' => true])
            ->addAttributeToFilter('orderflow_export_date', ['lt' => new \Zend_Db_Expr('updated_at')])
            ->addFieldToFilter('orderflow_export_status', ['neq' => 'Queued'])
            ->setStore($this->getStoreId($website->getId()))
            ->setPage(1, $this->getBatchSize($website->getId()));

        return $products;
    }
}