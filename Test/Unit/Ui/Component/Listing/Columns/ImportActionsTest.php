<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Ui\Component\Listing\Columns;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use RealtimeDespatch\OrderFlow\Ui\Component\Listing\Columns\ImportActions;

/**
 * Class ImportActionsTest
 * @package RealtimeDespatch\OrderFlow\Test\Unit\Ui\Component\Listing\Columns
 */
class ImportActionsTest extends \PHPUnit\Framework\TestCase
{
    protected ImportActions $column;
    protected ContextInterface $mockContext;
    protected UiComponentFactory $mockUiComponentFactory;
    protected AuthorizationInterface $mockAuth;
    protected UrlInterface $mockUrl;

    protected function setUp(): void
    {
        $this->mockContext = $this->createMock(ContextInterface::class);
        $this->mockUiComponentFactory = $this->createMock(UiComponentFactory::class);
        $this->mockAuth = $this->createMock(AuthorizationInterface::class);
        $this->mockUrl = $this->createMock(UrlInterface::class);

        $this->mockAuth
            ->expects($this->once())
            ->method('isAllowed')
            ->with('RealtimeDespatch_OrderFlow::orderflow_requests_imports')
            ->willReturn(true);

        $this->column = new ImportActions(
            $this->mockContext,
            $this->mockUiComponentFactory,
            $this->mockUrl,
            $this->mockAuth,
            [],
            ['name' => 'import_actions'],
        );
    }

    public function testPrepareDataSource(): void
    {
        $this->mockUrl
            ->expects($this->exactly(2))
            ->method('getUrl')
            ->withConsecutive(
                ['orderflow/import/view', ['import_id' => 1]],
                ['orderflow/request/view', ['request_id' => 2]]
            )
            ->willReturnOnConsecutiveCalls(
                'https://example.com/orderflow/import/view/import_id/1',
                'https://example.com/orderflow/request/view/request_id/1'
            );

        $result = $this->column->prepareDataSource([
            'data' => [
                'items' => [
                    [
                        'import_id' => 1,
                        'request_id' => 2,
                        'import_actions' => [],
                    ],
                ]
            ]
        ]);

        $this->assertEquals(
            [
                'href' => 'https://example.com/orderflow/import/view/import_id/1',
                'label' => __('View Import'),
            ],
            $result['data']['items'][0]['import_actions']['view_import']
        );

        $this->assertEquals(
            [
                'href' => 'https://example.com/orderflow/request/view/request_id/1',
                'label' => __('View Processed Request'),
            ],
            $result['data']['items'][0]['import_actions']['view_request']
        );
    }

    public function testCanViewRequest(): void
    {
        $this->assertTrue($this->column->canViewRequest());
    }
}