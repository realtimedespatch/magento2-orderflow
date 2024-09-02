<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Ui\Component\Listing\Columns;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use RealtimeDespatch\OrderFlow\Ui\Component\Listing\Columns\RequestImportActions;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Import as ImportResource;

/**
 * Class RequestImportActionsTest
 * @package RealtimeDespatch\OrderFlow\Test\Unit\Ui\Component\Listing\Columns
 */
class RequestImportActionsTest extends \PHPUnit\Framework\TestCase
{
    protected RequestImportActions $column;
    protected ContextInterface $mockContext;
    protected UiComponentFactory $mockUiComponentFactory;
    protected AuthorizationInterface $mockAuth;
    protected UrlInterface $mockUrl;
    protected ImportResource $mockImportResource;

    protected function setUp(): void
    {
        $this->mockContext = $this->createMock(ContextInterface::class);
        $this->mockUiComponentFactory = $this->createMock(UiComponentFactory::class);
        $this->mockAuth = $this->createMock(AuthorizationInterface::class);
        $this->mockUrl = $this->createMock(UrlInterface::class);
        $this->mockImportResource = $this->createMock(ImportResource::class);

        $this->column = new RequestImportActions(
            $this->mockContext,
            $this->mockUiComponentFactory,
            $this->mockUrl,
            $this->mockAuth,
            $this->mockImportResource,
            [],
            ['name' => 'request_import_actions'],
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
                ['orderflow/import/view', ['import_id' => 2]],
            )
            ->willReturnOnConsecutiveCalls(
                'https://example.com/orderflow/request/view/request_id/1',
                'https://example.com/orderflow/request/process/request_id/1',
                'https://example.com/orderflow/import/view/import_id/2'
            );

        $this->mockImportResource
            ->expects($this->once())
            ->method('getIdByRequestId')
            ->with(1)
            ->willReturn(2);

        $this->mockAuth
            ->expects($this->once())
            ->method('isAllowed')
            ->with('RealtimeDespatch_OrderFlow::orderflow_imports_inventory')
            ->willReturn(true);

        $result = $this->column->prepareDataSource([
            'data' => [
                'items' => [
                    [
                        'entity' => 'Inventory',
                        'request_id' => 1,
                        'request_import_actions' => [],
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
            $result['data']['items'][0]['request_import_actions']['view']
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
            $result['data']['items'][0]['request_import_actions']['process']
        );

        $this->assertEquals(
            [
                'href' => 'https://example.com/orderflow/import/view/import_id/2',
                'label' => __('View Import Report'),
            ],
            $result['data']['items'][0]['request_import_actions']['view_import']
        );
    }

    /**
     * @dataProvider canViewImportDataProvider
     * @param string $entity
     * @param string|null $permission
     * @return void
     */
    public function testCanViewImport(string $entity, ?string $permission): void
    {
        if ($permission) {
            $this->mockAuth
                ->expects($this->once())
                ->method('isAllowed')
                ->with($permission)
                ->willReturn(true);
            $this->assertTrue($this->column->canViewImport($entity));
        } else {
            $this->assertFalse($this->column->canViewImport($entity));
        }
    }

    public function canViewImportDataProvider(): array
    {
        return [
            ['Inventory', 'RealtimeDespatch_OrderFlow::orderflow_imports_inventory'],
            ['Shipment', 'RealtimeDespatch_OrderFlow::orderflow_imports_shipments'],
            ['Invalid', null],
        ];
    }
}