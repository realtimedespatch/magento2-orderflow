<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Plugin\Adminhtml;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\UrlInterface;
use RealtimeDespatch\OrderFlow\Helper\Export\Order;
use RealtimeDespatch\OrderFlow\Plugin\Adminhtml\OrderActions;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class OrderActionsTest extends \PHPUnit\Framework\TestCase
{
    protected OrderActions $plugin;
    protected ContextInterface $mockContext;
    protected UrlInterface $mockUrl;
    protected AuthorizationInterface $mockAuthorization;
    protected Order $mockOrderHelper;

    protected function setUp(): void
    {
        $this->mockContext = $this->createMock(ContextInterface::class);
        $this->mockUrl = $this->createMock(UrlInterface::class);
        $this->mockAuthorization = $this->createMock(AuthorizationInterface::class);
        $this->mockOrderHelper = $this->createMock(Order::class);

        $this->plugin = new OrderActions(
            $this->mockContext,
            $this->mockUrl,
            $this->mockOrderHelper,
            $this->mockAuthorization
        );
    }

    /**
     * @dataProvider dataProvider
     * @param bool $isAllowed
     * @return void
     */
    public function testAfterPrepareDataSources(
        bool $isAllowed,
    ): void
    {
        $mockViewAction = $this->createMock(\Magento\Sales\Ui\Component\Listing\Column\ViewAction::class);

        $storeId = rand(1, 10);
        $entityId = rand(1, 500);

        $this->mockContext
            ->expects($this->once())
            ->method('getFilterParam')
            ->with('store_id')
            ->willReturn($storeId);

        if (!$isAllowed) {
            $this->mockAuthorization
                ->expects($this->once())
                ->method('isAllowed')
                ->with('RealtimeDespatch_OrderFlow::orderflow_exports_products')
                ->willReturn(false);

            $this->mockUrl
                ->expects($this->never())
                ->method('getUrl');
        } else {

            $this->mockAuthorization
                ->expects($this->once())
                ->method('isAllowed')
                ->with('RealtimeDespatch_OrderFlow::orderflow_exports_products')
                ->willReturn(true);

            $mockViewAction
                ->expects($this->once())
                ->method('getData')
                ->with('name')
                ->willReturn('view');

            $this->mockUrl
                ->expects($this->once())
                ->method('getUrl')
                ->with(
                    'orderflow/order/export',
                    ['order_id' => $entityId, 'store' => $storeId]
                )
                ->willReturn("http://localhost/orderflow/order/export/order_id/{$entityId}/store/{$storeId}");


        }

        $result = $this->plugin->afterPrepareDataSource(
            $mockViewAction,
            [
                'data' => [
                    'items' => [
                        [
                            'entity_id' => $entityId,
                            'view' => [],
                        ],
                    ],
                ],
            ]
        );

        if ($isAllowed) {
            $this->assertEquals(
                [
                    'data' => [
                        'items' => [
                            [
                                'entity_id' => $entityId,
                                'view' => [
                                    'export' => [
                                        'href' => "http://localhost/orderflow/order/export/order_id/{$entityId}/store/{$storeId}",
                                        'label' => __('Export'),
                                        'hidden' => false,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                $result
            );
        }
    }

    public function dataProvider(): array
    {
        return [
            [true],
            [false],
        ];
    }
}