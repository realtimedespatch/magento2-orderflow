<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model;

use Magento\Catalog\Api\AttributeSetRepositoryInterface;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Api\Data\ProductCustomOptionInterface;
use Magento\Catalog\Api\ProductAttributeManagementInterface;
use Magento\Catalog\Api\ProductRepositoryInterface as CatalogProductRepositoryInterface;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Eav\Api\Data\AttributeOptionInterface;
use Magento\Eav\Api\Data\AttributeSetInterface;
use Magento\Framework\Api\AttributeValue;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\ObjectManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Model\Data\Product;
use RealtimeDespatch\OrderFlow\Model\ProductRepository;

class ProductRepositoryTest extends TestCase
{
    /**
     * @var CatalogProductRepositoryInterface&MockObject
     */
    private $catalogProductRepository;

    /**
     * @var AttributeRepositoryInterface&MockObject
     */
    private $attributeRepository;

    /**
     * @var AttributeSetRepositoryInterface&MockObject
     */
    private $attributeSetRepository;

    /**
     * @var ProductAttributeManagementInterface&MockObject
     */
    private $productAttributeManagement;

    /**
     * @var ObjectManagerInterface&MockObject
     */
    private $objectManager;

    /**
     * @var ScopeConfigInterface&MockObject
     */
    private $scopeConfig;

    /**
     * @var ProductRepository
     */
    private $repository;

    protected function setUp(): void
    {
        $this->catalogProductRepository = $this->createMock(CatalogProductRepositoryInterface::class);
        $this->attributeRepository = $this->createMock(AttributeRepositoryInterface::class);
        $this->attributeSetRepository = $this->createMock(AttributeSetRepositoryInterface::class);
        $this->productAttributeManagement = $this->createMock(ProductAttributeManagementInterface::class);
        $this->objectManager = $this->createMock(ObjectManagerInterface::class);
        $this->scopeConfig = $this->createMock(ScopeConfigInterface::class);

        $this->repository = new ProductRepository(
            $this->catalogProductRepository,
            $this->attributeRepository,
            $this->attributeSetRepository,
            $this->productAttributeManagement,
            $this->objectManager,
            $this->scopeConfig
        );
    }

    public function testGetReturnsMappedOptionLabelsAndAttributeSetName(): void
    {
        $product = $this->createMock(\Magento\Catalog\Model\Product::class);
        $exportProduct = $this->createMock(Product::class);
        $colorAttribute = $this->createConfiguredMock(ProductAttributeInterface::class, [
            'getAttributeCode' => 'color',
            'getFrontendInput' => 'select',
            'getOptions' => [$this->createMock(ProductCustomOptionInterface::class)],
        ]);
        $materialAttribute = $this->createConfiguredMock(ProductAttributeInterface::class, [
            'getAttributeCode' => 'material',
            'getFrontendInput' => 'multiselect',
            'getOptions' => [$this->createMock(ProductCustomOptionInterface::class)],
        ]);
        $attributeSet = $this->createConfiguredMock(AttributeSetInterface::class, [
            'getAttributeSetName' => 'Fabric',
        ]);

        $colorOptions = [
            $this->createOption('12', 'Blue'),
        ];
        $materialOptions = [
            $this->createOption('5', 'Cotton'),
            $this->createOption('7', 'Linen'),
        ];

        $this->catalogProductRepository->expects($this->once())
            ->method('get')
            ->with('SKU-1', false, null)
            ->willReturn($product);
        $this->scopeConfig->expects($this->once())
            ->method('isSetFlag')
            ->with('orderflow_product_export/settings/use_attribute_option_labels')
            ->willReturn(true);
        $product->method('getAttributeSetId')->willReturn(42);
        $this->productAttributeManagement->expects($this->once())
            ->method('getAttributes')
            ->with(42)
            ->willReturn([$colorAttribute, $materialAttribute]);
        $this->attributeSetRepository->expects($this->once())
            ->method('get')
            ->with(42)
            ->willReturn($attributeSet);
        $this->attributeRepository->expects($this->exactly(2))
            ->method('get')
            ->willReturnMap([
                ['catalog_product', 'color', $this->createAttributeWithOptions($colorOptions)],
                ['catalog_product', 'material', $this->createAttributeWithOptions($materialOptions)],
            ]);
        $product->method('getData')
            ->willReturnCallback(function (...$args) {
                $key = $args[0] ?? null;
                if ($key === 'color') {
                    return '12';
                }
                if ($key === 'material') {
                    return '5,7';
                }
                if ($key === null) {
                    return ['sku' => 'SKU-1', 'attribute_set_id' => 42];
                }

                return null;
            });
        $product->expects($this->exactly(2))
            ->method('setCustomAttribute')
            ->withConsecutive(
                ['color', 'Blue'],
                ['material', ['Cotton', 'Linen']]
            );
        $product->expects($this->once())
            ->method('getCustomAttributes')
            ->willReturn([
                $this->createConfiguredMock(AttributeValue::class, [
                    'getAttributeCode' => 'color',
                    'getValue' => 'Blue',
                ]),
                $this->createConfiguredMock(AttributeValue::class, [
                    'getAttributeCode' => 'material',
                    'getValue' => ['Cotton', 'Linen'],
                ]),
            ]);
        $product->expects($this->once())
            ->method('getExtensionAttributes')
            ->willReturn(null);

        $this->objectManager->expects($this->once())
            ->method('create')
            ->with(Product::class)
            ->willReturn($exportProduct);
        $exportProduct->expects($this->once())
            ->method('setData')
            ->willReturnSelf();
        $exportProduct->expects($this->exactly(2))
            ->method('setCustomAttribute')
            ->withConsecutive(
                ['color', 'Blue'],
                ['material', ['Cotton', 'Linen']]
            )
            ->willReturnSelf();
        $exportProduct->expects($this->once())
            ->method('setAttributeSetName')
            ->with('Fabric')
            ->willReturnSelf();

        $result = $this->repository->get('SKU-1');

        $this->assertSame($exportProduct, $result);
    }

    public function testGetFallsBackToRawOptionValueWhenNoLabelExists(): void
    {
        $product = $this->createMock(\Magento\Catalog\Model\Product::class);
        $exportProduct = $this->createMock(Product::class);
        $attribute = $this->createConfiguredMock(ProductAttributeInterface::class, [
            'getAttributeCode' => 'color',
            'getFrontendInput' => 'select',
            'getOptions' => [$this->createMock(ProductCustomOptionInterface::class)],
        ]);
        $attributeSet = $this->createConfiguredMock(AttributeSetInterface::class, [
            'getAttributeSetName' => 'Default',
        ]);

        $this->catalogProductRepository->method('get')->willReturn($product);
        $this->scopeConfig->expects($this->once())
            ->method('isSetFlag')
            ->with('orderflow_product_export/settings/use_attribute_option_labels')
            ->willReturn(true);
        $product->method('getAttributeSetId')->willReturn(3);
        $this->productAttributeManagement->method('getAttributes')->willReturn([$attribute]);
        $this->attributeSetRepository->method('get')->willReturn($attributeSet);
        $this->attributeRepository->method('get')->willReturn($this->createAttributeWithOptions([]));
        $product->method('getData')
            ->willReturnCallback(function (...$args) {
                $key = $args[0] ?? null;
                if ($key === 'color') {
                    return '999';
                }
                if ($key === null) {
                    return ['sku' => 'SKU-2', 'attribute_set_id' => 3];
                }

                return null;
            });
        $product->expects($this->once())
            ->method('setCustomAttribute')
            ->with('color', '999');
        $product->method('getCustomAttributes')->willReturn([]);
        $product->method('getExtensionAttributes')->willReturn(null);

        $this->objectManager->method('create')->with(Product::class)->willReturn($exportProduct);
        $exportProduct->method('setData')->willReturnSelf();
        $exportProduct->expects($this->never())->method('setCustomAttribute');
        $exportProduct->method('setExtensionAttributes')->willReturnSelf();
        $exportProduct->method('setAttributeSetName')->willReturnSelf();

        $this->assertSame($exportProduct, $this->repository->get('SKU-2'));
    }

    public function testGetSkipsAttributeValueMappingWhenConfigDisabled(): void
    {
        $product = $this->createMock(\Magento\Catalog\Model\Product::class);
        $exportProduct = $this->createMock(Product::class);
        $attributeSet = $this->createConfiguredMock(AttributeSetInterface::class, [
            'getAttributeSetName' => 'Default',
        ]);

        $this->catalogProductRepository->expects($this->once())
            ->method('get')
            ->with('SKU-3', false, null)
            ->willReturn($product);
        $this->scopeConfig->expects($this->once())
            ->method('isSetFlag')
            ->with('orderflow_product_export/settings/use_attribute_option_labels')
            ->willReturn(false);
        $product->method('getAttributeSetId')->willReturn(9);
        $this->productAttributeManagement->expects($this->never())->method('getAttributes');
        $this->attributeRepository->expects($this->never())->method('get');
        $product->expects($this->never())->method('setCustomAttribute');
        $this->attributeSetRepository->expects($this->once())
            ->method('get')
            ->with(9)
            ->willReturn($attributeSet);
        $product->method('getData')->willReturn(['sku' => 'SKU-3', 'attribute_set_id' => 9]);
        $product->method('getCustomAttributes')->willReturn([]);
        $product->method('getExtensionAttributes')->willReturn(null);

        $this->objectManager->expects($this->once())
            ->method('create')
            ->with(Product::class)
            ->willReturn($exportProduct);
        $exportProduct->expects($this->once())->method('setData')->willReturnSelf();
        $exportProduct->expects($this->never())->method('setCustomAttribute');
        $exportProduct->expects($this->once())->method('setAttributeSetName')->with('Default')->willReturnSelf();

        $this->assertSame($exportProduct, $this->repository->get('SKU-3'));
    }

    public function testGetCopiesSequentialCustomAttributesToExportProduct(): void
    {
        $product = $this->createMock(\Magento\Catalog\Model\Product::class);
        $exportProduct = $this->createMock(Product::class);
        $attributeSet = $this->createConfiguredMock(AttributeSetInterface::class, [
            'getAttributeSetName' => 'Default',
        ]);

        $this->catalogProductRepository->expects($this->once())
            ->method('get')
            ->with('SKU-4', false, null)
            ->willReturn($product);
        $this->scopeConfig->expects($this->once())
            ->method('isSetFlag')
            ->with('orderflow_product_export/settings/use_attribute_option_labels')
            ->willReturn(false);
        $product->method('getAttributeSetId')->willReturn(11);
        $this->productAttributeManagement->expects($this->never())->method('getAttributes');
        $this->attributeSetRepository->expects($this->once())->method('get')->with(11)->willReturn($attributeSet);
        $product->method('getData')->willReturn(['sku' => 'SKU-4', 'attribute_set_id' => 11]);
        $product->method('getCustomAttributes')->willReturn([
            $this->createConfiguredMock(AttributeValue::class, [
                'getAttributeCode' => 'brand',
                'getValue' => 'Sirdar',
            ]),
            $this->createConfiguredMock(AttributeValue::class, [
                'getAttributeCode' => 'condition',
                'getValue' => 'New',
            ]),
        ]);
        $product->method('getExtensionAttributes')->willReturn(null);

        $this->objectManager->expects($this->once())
            ->method('create')
            ->with(Product::class)
            ->willReturn($exportProduct);
        $exportProduct->expects($this->once())->method('setData')->willReturnSelf();
        $exportProduct->expects($this->exactly(2))
            ->method('setCustomAttribute')
            ->withConsecutive(
                ['brand', 'Sirdar'],
                ['condition', 'New']
            )
            ->willReturnSelf();
        $exportProduct->expects($this->once())->method('setAttributeSetName')->with('Default')->willReturnSelf();

        $this->assertSame($exportProduct, $this->repository->get('SKU-4'));
    }

    private function createOption(string $value, string $label): AttributeOptionInterface
    {
        return $this->createConfiguredMock(AttributeOptionInterface::class, [
            'getValue' => $value,
            'getLabel' => $label,
        ]);
    }

    private function createAttributeWithOptions(array $options): \Magento\Catalog\Api\Data\ProductAttributeInterface
    {
        return $this->createConfiguredMock(ProductAttributeInterface::class, [
            'getOptions' => $options,
        ]);
    }
}
