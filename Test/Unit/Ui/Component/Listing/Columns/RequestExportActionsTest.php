<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Ui\Component\Listing\Columns;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use RealtimeDespatch\OrderFlow\Ui\Component\Listing\Columns\RequestExportActions;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Export as ExportResource;

/**
 * Class RequestExportActionsTest
 * @package RealtimeDespatch\OrderFlow\Test\Unit\Ui\Component\Listing\Columns
 */
class RequestExportActionsTest extends \PHPUnit\Framework\TestCase
{
    protected RequestExportActions $column;
    protected ContextInterface $mockContext;
    protected UiComponentFactory $mockUiComponentFactory;
    protected AuthorizationInterface $mockAuth;
    protected UrlInterface $mockUrl;
    protected ExportResource $mockExportResource;

    protected function setUp(): void
    {
        $this->mockContext = $this->createMock(ContextInterface::class);
        $this->mockUiComponentFactory = $this->createMock(UiComponentFactory::class);
        $this->mockAuth = $this->createMock(AuthorizationInterface::class);
        $this->mockUrl = $this->createMock(UrlInterface::class);
        $this->mockExportResource = $this->createMock(ExportResource::class);

        $this->column = new RequestExportActions(
            $this->mockContext,
            $this->mockUiComponentFactory,
            $this->mockUrl,
            $this->mockAuth,
            $this->mockExportResource,
            [],
            ['name' => 'request_export_actions'],
        );
    }

    public function testPrepareDataSource(): void
    {
        $this->mockUrl
            ->expects($this->exactly(3))
            ->method('getUrl')
            ->withConsecutive(
                ['orderflow/request/view', ['request_id' => 1]],
                ['orderflow/request/process', ['request_id' => 1]],
                ['orderflow/export/view', ['export_id' => 2]],
            )
            ->willReturnOnConsecutiveCalls(
                'https://example.com/orderflow/request/view/request_id/1',
                'https://example.com/orderflow/request/process/request_id/1',
                'https://example.com/orderflow/export/view/export_id/2'
            );

        $this->mockExportResource
            ->expects($this->once())
            ->method('getIdByRequestId')
            ->with(1)
            ->willReturn(2);

        $this->mockAuth
            ->expects($this->once())
            ->method('isAllowed')
            ->with('RealtimeDespatch_OrderFlow::orderflow_exports_orders')
            ->willReturn(true);

        $result = $this->column->prepareDataSource([
            'data' => [
                'items' => [
                    [
                        'entity' => 'Order',
                        'request_id' => 1,
                        'request_export_actions' => [],
                        'processed_at' => 'Pending',
                    ],
                ]
            ]
        ]);

        $this->assertEquals(
            [
                'href' => 'https://example.com/orderflow/request/view/request_id/1',
                'label' => __('View Request'),
            ],
            $result['data']['items'][0]['request_export_actions']['view']
        );

        $this->assertEquals(
            [
                'href' => 'https://example.com/orderflow/request/process/request_id/1',
                'label' => __('Process'),
                'confirm' => [
                    'title' => __('Process Request'),
                    'message' => __('Are you sure you wish to process this request?'),
                ]
            ],
            $result['data']['items'][0]['request_export_actions']['process']
        );

        $this->assertEquals(
            [
                'href' => 'https://example.com/orderflow/export/view/export_id/2',
                'label' => __('View Export Report'),
            ],
            $result['data']['items'][0]['request_export_actions']['view_export']
        );
    }

    /**
     * @dataProvider canViewExportDataProvider
     * @param string $entity
     * @param string|null $permission
     * @return void
     */
    public function testCanViewExport(string $entity, ?string $permission): void
    {
        if ($permission) {
            $this->mockAuth
                ->expects($this->once())
                ->method('isAllowed')
                ->with($permission)
                ->willReturn(true);
            $this->assertTrue($this->column->canViewExport($entity));
        } else {
            $this->assertFalse($this->column->canViewExport($entity));
        }
    }

    public function canViewExportDataProvider(): array
    {
        return [
            ['Order', 'RealtimeDespatch_OrderFlow::orderflow_exports_orders'],
            ['Product', 'RealtimeDespatch_OrderFlow::orderflow_exports_products'],
            ['Invalid', null],
        ];
    }
}