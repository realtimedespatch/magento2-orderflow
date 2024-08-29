<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Request;

use Magento\Backend\Block\Widget\Button\ButtonList;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use RealtimeDespatch\OrderFlow\Block\Adminhtml\Request\View;
use RealtimeDespatch\OrderFlow\Model\Request;
use RealtimeDespatch\OrderFlow\Model\RequestRepository;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Export;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Import;

/**
 * Class ViewTest
 * @package RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Request
 */
class ViewTest extends \PHPUnit\Framework\TestCase
{
    protected Context $mockContext;
    protected Registry $mockRegistry;
    protected Export $mockExportResource;
    protected Import $mockImportResource;
    protected AuthorizationInterface $mockAuthorization;
    protected Request $mockRequest;
    protected UrlInterface $mockUrl;
    protected ButtonList $mockButtonList;

    protected function setUp(): void
    {
        $this->mockContext = $this->createMock(Context::class);
        $this->mockRegistry = $this->createMock(Registry::class);
        $this->mockExportResource = $this->createMock(Export::class);
        $this->mockImportResource = $this->createMock(Import::class);
        $this->mockAuthorization = $this->createMock(AuthorizationInterface::class);
        $this->mockRequest = $this->createMock(Request::class);
        $this->mockUrl = $this->createMock(UrlInterface::class);
        $this->mockButtonList = $this->createMock(ButtonList::class);

        $this->mockContext
            ->method('getRequest')
            ->willReturn($this->createMock(RequestInterface::class));

        $this->mockContext
            ->method('getUrlBuilder')
            ->willReturn($this->mockUrl);

        $this->mockContext
            ->method('getButtonList')
            ->willReturn($this->mockButtonList);

        $this->mockContext
            ->method('getAuthorization')
            ->willReturn($this->mockAuthorization);
    }

    /**
     * @dataProvider canViewExportProvider
     */
    public function testCanViewExportProduct(string $type, string $permission, bool $result): void
    {
        $this->mockAuthorization
            ->expects($this->exactly(($result) ? 1 : 0))
            ->method('isAllowed')
            ->with($permission)
            ->willReturn(true);

        $this->mockRegistry
            ->method('registry')
            ->with('current_request')
            ->willReturn($this->mockRequest);

        $this->mockRequest
            ->method('getType')
            ->willReturn($type);

        $block = new \RealtimeDespatch\OrderFlow\Block\Adminhtml\Request\View(
            $this->mockContext,
            $this->mockRegistry,
            $this->mockExportResource,
            $this->mockImportResource
        );

        $this->assertEquals($result, $block->canViewExport($type));
    }

    /**
     * @dataProvider canViewImportProvider
     */
    public function testCanViewImports(string $type, string $permission, bool $result): void
    {
        $this->mockAuthorization
            ->expects($this->exactly(($result) ? 1 : 0))
            ->method('isAllowed')
            ->with($permission)
            ->willReturn(true);

        $this->mockRegistry
            ->method('registry')
            ->with('current_request')
            ->willReturn($this->mockRequest);

        $this->mockRequest
            ->method('getType')
            ->willReturn($type);

        $block = new \RealtimeDespatch\OrderFlow\Block\Adminhtml\Request\View(
            $this->mockContext,
            $this->mockRegistry,
            $this->mockExportResource,
            $this->mockImportResource
        );

        $this->assertEquals($result, $block->canViewImport($type));
    }

    public function testSetImportViewButton(): void
    {
        $this->mockRegistry
            ->method('registry')
            ->with('current_request')
            ->willReturn($this->mockRequest);

        $this->mockRequest
            ->method('getType')
            ->willReturn('Import');

        $this->mockRequest
            ->method('getEntity')
            ->willReturn('Shipment');

        $this->mockRequest
            ->expects($this->once())
            ->method('isExport')
            ->willReturn(false);

        $this->mockRequest
            ->method('getId')
            ->willReturn(1);

        $this->mockImportResource
            ->expects($this->once())
            ->method('getIdByRequestId')
            ->with(1)
            ->willReturn(2);

        $this->mockAuthorization
            ->expects($this->once())
            ->method('isAllowed')
            ->with('RealtimeDespatch_OrderFlow::orderflow_imports_shipments')
            ->willReturn(true);


        $this->mockUrl
            ->method('getUrl')
            ->withConsecutive(
                ['orderflow/request/index/type/import'],
                ['orderflow/import/view', ['import_id' => 2]]
            )
            ->willReturn('url');

        $this->mockButtonList
            ->method('add')
            ->withConsecutive(
                [
                    'back',
                    [
                        'label' => __('Back'),
                        'onclick' => 'setLocation(\'url\')',
                        'class' => 'back',
                    ]
                ],
                [
                    'reset',
                    [
                        'label' => __('Reset'),
                        'onclick' => 'setLocation(window.location.href)',
                        'class' => 'reset',
                    ]
                ],
                [
                    'save',
                    [
                        'label' => __('Save'),
                        'class' => 'save primary',
                        'data_attribute' => [
                            'mage-init' => ['button' => ['event' => 'save', 'target' => '#edit_form']],
                        ]
                    ]
                ],
                [
                    'import_vew',
                    [
                        'label' => __('View Import Report'),
                        'class' => 'import',
                        'id' => 'request-view-import-button',
                        'onclick' => 'setLocation(\'url\')',
                        'sort_order' => 0
                    ]
                ],
            );

        new \RealtimeDespatch\OrderFlow\Block\Adminhtml\Request\View(
            $this->mockContext,
            $this->mockRegistry,
            $this->mockExportResource,
            $this->mockImportResource
        );
    }

    public function testSetExportViewButton(): void
    {
        $this->mockRequest
            ->method('getType')
            ->willReturn('Export');

        $this->mockRequest
            ->method('getEntity')
            ->willReturn('Order');

        $this->mockRequest
            ->expects($this->once())
            ->method('isExport')
            ->willReturn(true);

        $this->mockRequest
            ->method('getId')
            ->willReturn(1);

        $this->mockExportResource
            ->expects($this->once())
            ->method('getIdByRequestId')
            ->with(1)
            ->willReturn(2);

        $this->mockAuthorization
            ->expects($this->once())
            ->method('isAllowed')
            ->with('RealtimeDespatch_OrderFlow::orderflow_exports_orders')
            ->willReturn(true);


        $this->mockUrl
            ->method('getUrl')
            ->withConsecutive(
                ['orderflow/request/index/type/export'],
                ['orderflow/export/view', ['export_id' => 2]]
            )
            ->willReturn('url');

        $this->mockButtonList
            ->method('add')
            ->withConsecutive(
                [
                    'back',
                    [
                        'label' => __('Back'),
                        'onclick' => 'setLocation(\'url\')',
                        'class' => 'back',
                    ]
                ],
                [
                    'reset',
                    [
                        'label' => __('Reset'),
                        'onclick' => 'setLocation(window.location.href)',
                        'class' => 'reset',
                    ]
                ],
                [
                    'save',
                    [
                        'label' => __('Save'),
                        'class' => 'save primary',
                        'data_attribute' => [
                            'mage-init' => ['button' => ['event' => 'save', 'target' => '#edit_form']],
                        ]
                    ]
                ],
                [
                    'export_vew',
                    [
                        'label' => __('View Export Report'),
                        'class' => 'export',
                        'id' => 'request-view-export-button',
                        'onclick' => 'setLocation(\'url\')',
                        'sort_order' => 0
                    ]
                ],
            );

        new \RealtimeDespatch\OrderFlow\Block\Adminhtml\Request\View(
            $this->mockContext,
            $this->mockRegistry,
            $this->mockExportResource,
            $this->mockImportResource,
            [
                'request' => $this->mockRequest
            ]
        );
    }

    public function testSetProcessButton(): void
    {
        $this->mockRegistry
            ->method('registry')
            ->willReturnCallback(function($arg) {
                if ($arg == 'current_request') {
                    return null;
                }
                if ($arg == 'request') {
                    return $this->mockRequest;
                }
                return null;
            });

        $this->mockRequest
            ->method('getType')
            ->willReturn('Export');

        $this->mockRequest
            ->expects($this->once())
            ->method('canProcess')
            ->willReturn(true);

        $this->mockRequest
            ->method('getId')
            ->willReturn(1);

        $this->mockUrl
            ->method('getUrl')
            ->withConsecutive(
                ['orderflow/request/index/type/export'],
                ['orderflow/request/process', ['request_id' => 1]]
            )
            ->willReturn('url');

        $this->mockButtonList
            ->method('add')
            ->withConsecutive(
                [
                    'back',
                    [
                        'label' => __('Back'),
                        'onclick' => 'setLocation(\'url\')',
                        'class' => 'back',
                    ]
                ],
                [
                    'reset',
                    [
                        'label' => __('Reset'),
                        'onclick' => 'setLocation(window.location.href)',
                        'class' => 'reset',
                    ]
                ],
                [
                    'save',
                    [
                        'label' => __('Save'),
                        'class' => 'save primary',
                        'data_attribute' => [
                            'mage-init' => ['button' => ['event' => 'save', 'target' => '#edit_form']],
                        ]
                    ]
                ],
                [
                    'request_process',
                    [
                        'label' => __('Process'),
                        'class' => 'process',
                        'id' => 'request-view-process-button',
                        'onclick' => 'confirmSetLocation(\'Are you sure you wish to process this request?\', \'url\')',
                        'sort_order' => 0
                    ]
                ],
            );

        new \RealtimeDespatch\OrderFlow\Block\Adminhtml\Request\View(
            $this->mockContext,
            $this->mockRegistry,
            $this->mockExportResource,
            $this->mockImportResource
        );
    }

    public function testGetRequestException(): void
    {
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Request Not Found');

        new View(
            $this->mockContext,
            $this->mockRegistry,
            $this->mockExportResource,
            $this->mockImportResource
        );
    }

    public function canViewExportProvider(): array
    {
        return [
            ['Product', 'RealtimeDespatch_OrderFlow::orderflow_exports_products', true],
            ['Order', 'RealtimeDespatch_OrderFlow::orderflow_exports_orders', true],
            ['Unknown', 'RealtimeDespatch_OrderFlow::orderflow_exports_unknown', false],
        ];
    }

    public function canViewImportProvider(): array
    {
        return [
            ['Inventory', 'RealtimeDespatch_OrderFlow::orderflow_imports_inventory', true],
            ['Shipment', 'RealtimeDespatch_OrderFlow::orderflow_imports_shipments', true],
            ['Unknown', 'RealtimeDespatch_OrderFlow::orderflow_imports_unknown', false],
        ];
    }
}