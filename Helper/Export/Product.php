<?php

namespace RealtimeDespatch\OrderFlow\Helper\Export;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Website;
use RealtimeDespatch\OrderFlow\Api\ExportHelperInterface;

/**
 * Product Export Helper.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Product extends AbstractHelper implements ExportHelperInterface
{
    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @param Context $context
     * @param CollectionFactory $productCollectionFactory
     */
    public function __construct(
        Context $context,
        CollectionFactory $productCollectionFactory
    ) {
        $this->productCollectionFactory = $productCollectionFactory;

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
            ScopeInterface::SCOPE_STORE,
            $scopeId
        );
    }

    /**
     * Returns the maximum batch size for processing.
     *
     * @param integer|null $scopeId
     *
     * @return int
     */
    public function getBatchSize($scopeId = null)
    {
        return (integer) $this->scopeConfig->getValue(
            'orderflow_product_export/settings/batch_size',
            ScopeInterface::SCOPE_WEBSITE,
            $scopeId
        );
    }

    /**
     * Returns the default store ID for a website.
     *
     * @param integer|null $scopeId
     *
     * @return int
     */
    public function getStoreId($scopeId = null)
    {
        return (integer) $this->scopeConfig->getValue(
            'orderflow_product_export/settings/store_id',
            ScopeInterface::SCOPE_WEBSITE,
            $scopeId
        );
    }

    /**
     * Returns a collection of creatable products.
     *
     * @param Website $website
     *
     * @return Collection
     */
    public function getCreateableProducts(Website $website)
    {
        return $this->productCollectionFactory
            ->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('type_id', ['eq' => 'simple'])
            ->addAttributeToFilter('orderflow_export_date', ['null' => true])
            ->addAttributeToFilter(
                [
                    ['attribute' => 'orderflow_export_status', 'null' => true],
                    ['attribute' => 'orderflow_export_status', ['neq' => ['Queued']]],
                ],
                '',
                'left'
            )
            ->setStore($this->getStoreId($website->getId()))
            ->setPage(1, $this->getBatchSize($website->getId()));
    }

    /**
     * Returns a collection of updateable products.
     *
     * @param Website $website
     *
     * @return Collection
     */
    public function getUpdateableProducts(Website $website)
    {
        return $this
            ->productCollectionFactory
            ->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('type_id', ['eq' => 'simple'])
            ->addAttributeToFilter('orderflow_export_date', ['notnull' => true])
            ->addFieldToFilter('orderflow_export_status', ['eq' => 'Pending'])
            ->setStore($this->getStoreId($website->getId()))
            ->setPage(1, $this->getBatchSize($website->getId()));
    }
}
