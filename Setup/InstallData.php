<?php

/** @noinspection PhpUndefinedClassInspection */

namespace RealtimeDespatch\OrderFlow\Setup;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order;
use Magento\Sales\Setup\SalesSetup;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use RealtimeDespatch\OrderFlow\Model\Product\Attribute\Source\ExportStatus;
use Zend_Validate_Exception;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Sales setup factory
     *
     * @var SalesSetupFactory
     */
    protected $salesSetupFactory;

    /**
     * Resource
     *
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * Resource
     *
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     * @param SalesSetupFactory $salesSetupFactory
     * @param ResourceConnection $resource
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        SalesSetupFactory $salesSetupFactory,
        ResourceConnection $resource,
        CollectionFactory $collectionFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @throws LocalizedException|Zend_Validate_Exception
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        /** @noinspection PhpUndefinedMethodInspection */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        /** @var SalesSetup $salesSetup */
        /** @noinspection PhpUndefinedMethodInspection */
        $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);

        //Schedule Design Update tab
        $eavSetup->addAttributeGroup(
            ProductAttributeInterface::ENTITY_TYPE_CODE,
            'Default',
            'OrderFlow',
            10
        );

        // Product Exported Attribute
        $eavSetup->addAttribute(
            Product::ENTITY,
            'orderflow_export_status',
            [
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Export Status',
                'input' => 'select',
                'class' => '',
                'source' => ExportStatus::class,
                'global' => Attribute::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => 'simple',
                'default' => 'Pending',
                'group' => 'OrderFlow',
                'is_filterable_in_grid' => true,
                'is_visible_in_grid' => true,
                'option' => [
                    'values' => [
                        'Pending',
                        'Queued',
                        'Exported',
                        'Failed'
                    ]
                ]
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'orderflow_export_date',
            [
                'type' => 'datetime',
                'backend' => '',
                'frontend' => '',
                'label' => 'Last Exported',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => Attribute::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => null,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'OrderFlow',
            ]
        );

        // Order Exported Attribute
        $salesSetup->addAttribute(
            Order::ENTITY,
            'orderflow_export_date',
            ['type' => 'datetime', 'visible' => true, 'required' => false]
        );

        $salesSetup->addAttribute(
            Order::ENTITY,
            'orderflow_export_status',
            ['type' => 'text', 'visible' => true, 'required' => false]
        );

        // Custom OrderFlow Product Attributes
        $eavSetup->addAttribute(
            Product::ENTITY,
            'barcode',
            [
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Barcode',
                'input' => 'text',
                'class' => '',
                'source' => null,
                'global' => Attribute::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => 'simple',
                'default' => null,
                'group' => 'OrderFlow',
                'is_filterable_in_grid' => false,
                'is_visible_in_grid' => false
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'weight_units',
            [
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Weight Units',
                'input' => 'text',
                'class' => '',
                'source' => null,
                'global' => Attribute::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => 'simple',
                'default' => null,
                'group' => 'OrderFlow',
                'is_filterable_in_grid' => false,
                'is_visible_in_grid' => false
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'length',
            [
                'type' => 'decimal',
                'backend' => '',
                'frontend' => '',
                'label' => 'Length',
                'input' => 'text',
                'class' => '',
                'source' => null,
                'global' => Attribute::SCOPE_WEBSITE,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => 'simple',
                'default' => null,
                'group' => 'OrderFlow',
                'is_filterable_in_grid' => false,
                'is_visible_in_grid' => false
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'width',
            [
                'type' => 'decimal',
                'backend' => '',
                'frontend' => '',
                'label' => 'Width',
                'input' => 'text',
                'class' => '',
                'source' => null,
                'global' => Attribute::SCOPE_WEBSITE,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => 'simple',
                'default' => null,
                'group' => 'OrderFlow',
                'is_filterable_in_grid' => false,
                'is_visible_in_grid' => false
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'height',
            [
                'type' => 'decimal',
                'backend' => '',
                'frontend' => '',
                'label' => 'Height',
                'input' => 'text',
                'class' => '',
                'source' => null,
                'global' => Attribute::SCOPE_WEBSITE,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => 'simple',
                'default' => null,
                'group' => 'OrderFlow',
                'is_filterable_in_grid' => false,
                'is_visible_in_grid' => false
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'area',
            [
                'type' => 'decimal',
                'backend' => '',
                'frontend' => '',
                'label' => 'Area',
                'input' => 'text',
                'class' => '',
                'source' => null,
                'global' => Attribute::SCOPE_WEBSITE,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => 'simple',
                'default' => null,
                'group' => 'OrderFlow',
                'is_filterable_in_grid' => false,
                'is_visible_in_grid' => false
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'volume',
            [
                'type' => 'decimal',
                'backend' => '',
                'frontend' => '',
                'label' => 'Volume',
                'input' => 'text',
                'class' => '',
                'source' => null,
                'global' => Attribute::SCOPE_WEBSITE,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => 'simple',
                'default' => null,
                'group' => 'OrderFlow',
                'is_filterable_in_grid' => false,
                'is_visible_in_grid' => false
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'storage_types',
            [
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Storage Types',
                'input' => 'text',
                'class' => '',
                'source' => null,
                'global' => Attribute::SCOPE_WEBSITE,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => 'simple',
                'default' => null,
                'group' => 'OrderFlow',
                'is_filterable_in_grid' => false,
                'is_visible_in_grid' => false
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'tax_code',
            [
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Tax Code',
                'input' => 'text',
                'class' => '',
                'source' => null,
                'global' => Attribute::SCOPE_WEBSITE,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => 'simple',
                'default' => null,
                'group' => 'OrderFlow',
                'is_filterable_in_grid' => false,
                'is_visible_in_grid' => false
            ]
        );
    }
}
