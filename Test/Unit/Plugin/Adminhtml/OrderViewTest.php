<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Plugin\Adminhtml;

use Magento\Framework\AuthorizationInterface;
use RealtimeDespatch\OrderFlow\Plugin\Adminhtml\OrderView;
use Magento\Sales\Block\Adminhtml\Order\View as AdminOrderViewBlock;

class OrderViewTest extends \PHPUnit\Framework\TestCase
{
    protected OrderView $plugin;
    protected AuthorizationInterface $mockAuthorization;
    protected AdminOrderViewBlock $mockOrderViewBlock;

    protected function setUp(): void
    {
        $this->mockAuthorization = $this->createMock(AuthorizationInterface::class);
        $this->mockOrderViewBlock = $this->createMock(AdminOrderViewBlock::class);

        $this->plugin = new OrderView(
            $this->mockAuthorization
        );
    }

    public function testBeforeSetLayout(): void
    {
        $this->mockAuthorization
            ->expects($this->once())
            ->method('isAllowed')
            ->with('RealtimeDespatch_OrderFlow::orderflow_exports_orders')
            ->willReturn(true);

        $expectedMessage = "Are you sure you wish to export this order?";
        $expectedUrl = "http://example.com/admin/orderflow/export/order/order_id/1";

        $this->mockOrderViewBlock
            ->expects($this->once())
            ->method('getUrl')
            ->with('orderflow/order/export')
            ->willReturn($expectedUrl);

        $this->mockOrderViewBlock
            ->expects($this->once())
            ->method('addButton')
            ->with(
                'order_export',
                [
                    'label' => __('Export'),
                    'class' => 'export',
                    'id' => 'order-view-export-button',
                    'onclick' => "confirmSetLocation('$expectedMessage', '$expectedUrl')",
                ]
            );

        $this->plugin->beforeSetLayout($this->mockOrderViewBlock);
    }

    public function testBeforeSetLayoutNotAllowed(): void
    {
        $this->mockAuthorization
            ->expects($this->once())
            ->method('isAllowed')
            ->with('RealtimeDespatch_OrderFlow::orderflow_exports_orders')
            ->willReturn(false);

        $this->mockOrderViewBlock
            ->expects($this->never())
            ->method('addButton');

        $this->plugin->beforeSetLayout($this->mockOrderViewBlock);
    }
}