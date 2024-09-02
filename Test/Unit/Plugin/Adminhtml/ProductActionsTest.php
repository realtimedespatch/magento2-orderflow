<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Plugin\Adminhtml;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\UrlInterface;
use RealtimeDespatch\OrderFlow\Plugin\Adminhtml\ProductActions;
use Magento\Catalog\Ui\Component\Listing\Columns\ProductActions as ProductActionsUiComponent;

class ProductActionsTest extends \PHPUnit\Framework\TestCase
{
    protected ContextInterface $mockContext;
    protected UrlInterface $mockUrl;
    protected AuthorizationInterface $mockAuthorization;
    protected ProductActions $plugin;
    protected ProductActionsUiComponent $subject;
    protected array $subjectResult;

    protected function setUp(): void
    {
        $this->mockContext = $this->createMock(ContextInterface::class);
        $this->mockUrl = $this->createMock(UrlInterface::class);
        $this->mockAuthorization = $this->createMock(AuthorizationInterface::class);
        $this->subject = $this->createMock(ProductActionsUiComponent::class);

        $this->subject
            ->method('getData')
            ->with('name')
            ->willReturn('actions');

        $this->mockContext
            ->method('getFilterParam')
            ->with('store_id')
            ->willReturn(1);

        $this->subjectResult = [
            'data' => [
                'items' => [
                    [
                        'entity_id' => 1,
                    ],
                    [
                        'entity_id' => 2,
                    ]
                ]
            ]
        ];

        $this->plugin = new ProductActions(
            $this->mockContext,
            $this->mockUrl,
            $this->mockAuthorization
        );
    }

    public function testAfterPrepareDataSourceNotAllowed(): void
    {
        $this->mockAuthorization
            ->method('isAllowed')
            ->with('RealtimeDespatch_OrderFlow::orderflow_exports_products')
            ->willReturn(false);

        $this->mockUrl
            ->expects($this->never())
            ->method('getUrl');

        $this->plugin->afterPrepareDataSource($this->subject, $this->subjectResult);
    }

    public function testAfterPrepareDataSource(): void
    {
        $this->mockAuthorization
            ->method('isAllowed')
            ->with('RealtimeDespatch_OrderFlow::orderflow_exports_products')
            ->willReturn(true);

        $this->mockUrl
            ->expects($this->exactly(2))
            ->method('getUrl')
            ->withConsecutive(
                ['orderflow/product/export', ['id' => 1, 'store' => 1]],
                ['orderflow/product/export', ['id' => 2, 'store' => 1]]
            )
            ->willReturnOnConsecutiveCalls(
                'http://example.com/admin/orderflow/export/product/product_id/1',
                'http://example.com/admin/orderflow/export/product/product_id/2'
            );

        $result = $this->plugin->afterPrepareDataSource($this->subject, $this->subjectResult);

        $this->assertEquals(
            [
                'data' => [
                    'items' => [
                        [
                            'entity_id' => 1,
                            'actions' => [
                                'export' => [
                                    'href' => 'http://example.com/admin/orderflow/export/product/product_id/1',
                                    'label' => __('Export'),
                                    'hidden' => false,
                                ]
                            ]
                        ],
                        [
                            'entity_id' => 2,
                            'actions' => [
                                'export' => [
                                    'href' => 'http://example.com/admin/orderflow/export/product/product_id/2',
                                    'label' => __('Export'),
                                    'hidden' => false,
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            $result
        );
    }
}