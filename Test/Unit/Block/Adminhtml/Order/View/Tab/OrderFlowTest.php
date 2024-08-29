<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Order\View\Tab;

use Magento\Sales\Model\Order;
use RealtimeDespatch\OrderFlow\Block\Adminhtml\Order\View\Tab\OrderFlow;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Sales\Helper\Admin as SalesAdminHelper;
use RealtimeDespatch\OrderFlow\Helper\Admin\Info as InfoHelper;
use RealtimeDespatch\OrderFlow\Helper\Api as ApiHelper;

/**
 * Class OrderFlowTest
 * @package RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Order\View\Tab
 */
class OrderFlowTest extends \PHPUnit\Framework\TestCase
{
    protected OrderFlow $block;
    protected Context $mockContext;
    protected Registry $mockRegistry;
    protected SalesAdminHelper $mockSalesAdminHelper;
    protected InfoHelper $mockInfoHelper;
    protected ApiHelper $mockApiHelper;
    protected Order $mockOrder;

    protected function setUp(): void
    {
        $this->mockContext = $this->createMock(Context::class);
        $this->mockRegistry = $this->createMock(Registry::class);
        $this->mockSalesAdminHelper = $this->createMock(SalesAdminHelper::class);
        $this->mockInfoHelper = $this->createMock(InfoHelper::class);
        $this->mockApiHelper = $this->createMock(ApiHelper::class);
        $this->mockOrder = $this->createMock(Order::class);

        $this->block = new OrderFlow(
            $this->mockContext,
            $this->mockRegistry,
            $this->mockSalesAdminHelper,
            $this->mockInfoHelper,
            $this->mockApiHelper
        );
    }

    public function testCanDisplayAdminInfo(): void
    {
        $this->mockInfoHelper
            ->method('isEnabled')
            ->willReturn(true);

        $this->assertTrue($this->block->canDisplayAdminInfo());
    }

    public function testGetOrderFlowOrderUrl(): void
    {
        $this->mockRegistry
            ->expects($this->once())
            ->method('registry')
            ->with('current_order')
            ->willReturn($this->mockOrder);

        $this->block->setStoreId(1);

        $this->mockApiHelper
            ->expects($this->once())
            ->method('getEndpoint')
            ->with(1)
            ->willReturn('http://www.example.com');

        $this->mockOrder
            ->expects($this->once())
            ->method('getIncrementId')
            ->willReturn('100000001');

        $this->mockApiHelper
            ->expects($this->once())
            ->method('getChannel')
            ->with(1)
            ->willReturn('test_channel');

        $url = $this->block->getOrderFlowOrderUrl();
        $this->assertEquals(
            'http://www.example.comdespatch/order/referenceDetail.htm?externalReference=100000001&channel=test_channel',
            $url
        );
    }

    public function testGetTabLabel(): void
    {
        $this->assertEquals('OrderFlow', $this->block->getTabLabel());
    }

    public function testGetTabTitle(): void
    {
        $this->assertEquals('OrderFlow Information', $this->block->getTabTitle());
    }

    public function testCanShowTab(): void
    {
        $this->assertTrue($this->block->canShowTab());
    }

    public function testIsHidden(): void
    {
        $this->assertFalse($this->block->isHidden());
    }
}