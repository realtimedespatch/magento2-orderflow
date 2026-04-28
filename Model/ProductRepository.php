<?php

namespace RealtimeDespatch\OrderFlow\Model;

use Magento\Catalog\Api\AttributeSetRepositoryInterface;
use Magento\Catalog\Api\ProductAttributeManagementInterface;
use Magento\Catalog\Api\ProductRepositoryInterface as CatalogProductRepositoryInterface;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\ObjectManagerInterface;
use RealtimeDespatch\OrderFlow\Api\ProductRepositoryInterface;

/**
 * OrderFlow product export repository.
 */
class ProductRepository implements ProductRepositoryInterface
{
    private const XML_PATH_USE_ATTRIBUTE_OPTION_LABELS = 'orderflow_product_export/settings/use_attribute_option_labels';

    /**
     * @var CatalogProductRepositoryInterface
     */
    protected $catalogProductRepository;

    /**
     * @var AttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * @var AttributeSetRepositoryInterface
     */
    protected $attributeSetRepository;

    /**
     * @var ProductAttributeManagementInterface
     */
    protected $productAttributeManagement;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $attributeOptionsMap = [];

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param CatalogProductRepositoryInterface $catalogProductRepository
     * @param AttributeRepositoryInterface $attributeRepository
     * @param AttributeSetRepositoryInterface $attributeSetRepository
     * @param ProductAttributeManagementInterface $productAttributeManagement
     * @param ObjectManagerInterface $objectManager
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        CatalogProductRepositoryInterface $catalogProductRepository,
        AttributeRepositoryInterface $attributeRepository,
        AttributeSetRepositoryInterface $attributeSetRepository,
        ProductAttributeManagementInterface $productAttributeManagement,
        ObjectManagerInterface $objectManager,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->catalogProductRepository = $catalogProductRepository;
        $this->attributeRepository = $attributeRepository;
        $this->attributeSetRepository = $attributeSetRepository;
        $this->productAttributeManagement = $productAttributeManagement;
        $this->objectManager = $objectManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @inheritdoc
     */
    public function get($sku, $storeId = null)
    {
        $product = $this->catalogProductRepository->get($sku, false, $storeId);
        $attributeSet = $this->attributeSetRepository->get($product->getAttributeSetId());

        if ($this->shouldUseAttributeOptionLabels()) {
            $attributes = $this->productAttributeManagement->getAttributes($product->getAttributeSetId());

            $sourceModelAttributes = array_filter($attributes, function ($attribute) {
                return sizeof($attribute->getOptions()) > 0;
            });

            foreach ($sourceModelAttributes as $attribute) {
                $attributeOptions = $this->getAttributeOptionsMap($attribute->getAttributeCode());
                $value = $product->getData($attribute->getAttributeCode());

                if ($attribute->getFrontendInput() == 'multiselect' && is_string($value)) {
                    $value = explode(',', $value);
                }

                if (is_array($value)) {
                    $newValue = [];
                    foreach ($value as $optionValue) {
                        $newValue[] = $attributeOptions[$optionValue] ?? $optionValue;
                    }
                    $product->setCustomAttribute($attribute->getAttributeCode(), $newValue);
                } else {
                    $product->setCustomAttribute($attribute->getAttributeCode(), $attributeOptions[$value] ?? $value);
                }
            }
        }

        $exportProduct = $this->objectManager->create(\RealtimeDespatch\OrderFlow\Model\Data\Product::class);
        $exportProduct->setData($product->getData());
        foreach ($product->getCustomAttributes() as $attributeCode => $customAttribute) {
            if (is_object($customAttribute) && method_exists($customAttribute, 'getAttributeCode')) {
                $attributeCode = $customAttribute->getAttributeCode();
            }
            if (!is_string($attributeCode) || $attributeCode === '') {
                continue;
            }
            $value = $customAttribute;
            if (is_object($customAttribute) && method_exists($customAttribute, 'getValue')) {
                $value = $customAttribute->getValue();
            }
            $exportProduct->setCustomAttribute($attributeCode, $value);
        }
        if ($product->getExtensionAttributes() !== null) {
            $exportProduct->setExtensionAttributes($product->getExtensionAttributes());
        }
        $exportProduct->setAttributeSetName($attributeSet->getAttributeSetName());

        return $exportProduct;
    }

    /**
     * @return bool
     */
    protected function shouldUseAttributeOptionLabels()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_USE_ATTRIBUTE_OPTION_LABELS);
    }

    /**
     * @param string $attributeCode
     *
     * @return array
     */
    protected function getAttributeOptionsMap($attributeCode)
    {
        if (!isset($this->attributeOptionsMap[$attributeCode])) {
            $attribute = $this->attributeRepository->get('catalog_product', $attributeCode);
            $options = [];
            foreach ($attribute->getOptions() as $option) {
                $options[$option->getValue()] = (string)$option->getLabel();
            }
            $this->attributeOptionsMap[$attributeCode] = $options;
        }

        return $this->attributeOptionsMap[$attributeCode];
    }
}
