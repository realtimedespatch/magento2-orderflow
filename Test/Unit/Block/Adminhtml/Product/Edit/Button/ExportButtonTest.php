<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Product\Edit\Button;

use Magento\Catalog\Model\Product;
use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Registry;
use RealtimeDespatch\OrderFlow\Block\Adminhtml\Product\Edit\Button\ExportButton;

/**
 * Class ExportButtonTest
 * @package RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Product\Edit\Button
 */
class ExportButtonTest extends \PHPUnit\Framework\TestCase
{
    protected ExportButton $block;
    protected Context $mockContext;
    protected Registry $mockRegistry;
    protected AuthorizationInterface $mockAuthorization;
    protected Product $mockProduct;

    protected function setUp(): void
    {
        $this->mockContext = $this->createMock(Context::class);
        $this->mockRegistry = $this->createMock(Registry::class);
        $this->mockAuthorization = $this->createMock(AuthorizationInterface::class);
        $this->mockProduct = $this->createMock(Product::class);

        $this->block = new ExportButton(
            $this->mockContext,
            $this->mockRegistry,
            $this->mockAuthorization
        );
    }

    public function testGetButtonData(): void
    {
        $this->mockAuthorization
            ->method('isAllowed')
            ->willReturn(true);

        $this->mockRegistry
            ->method('registry')
            ->with('current_product')
            ->willReturn($this->mockProduct);

        $this->mockProduct
            ->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $this->mockContext
            ->expects($this->once())
            ->method('getFilterParam')
            ->with('store_id')
            ->willReturn(2);

        $expectedUrl = 'http://localhost/orderflow/product/export/product_id/1/store_id/2';

        $this->mockContext
            ->expects($this->once())
            ->method('getUrl')
            ->with('orderflow/product/export', ['id' => 1, 'store' => 2])
            ->willReturn($expectedUrl);

        $result = $this->block->getButtonData();

        $this->assertEquals([
            'id' => 'product-view-export-button',
            'label' => __('Export'),
            'on_click' => 'confirmSetLocation(\'Are you sure you wish to export this product?\', \'' . $expectedUrl . '\')',
            'sort_order' => 10,
        ], $result);
    }

    public function testGetButtonDataNotAllowed(): void
    {
        $this->mockAuthorization
            ->method('isAllowed')
            ->with('RealtimeDespatch_OrderFlow::orderflow_exports_products')
            ->willReturn(false);

        $result = $this->block->getButtonData();

        $this->mockContext
            ->expects($this->never())
            ->method('getFilterParam');

        $this->assertNull($result);
    }
}