<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Import;

use Magento\Backend\Block\Widget\Button\ButtonList;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\AuthorizationInterface;
use RealtimeDespatch\OrderFlow\Block\Adminhtml\Import\View;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;
use RealtimeDespatch\OrderFlow\Model\Import;
use RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Import\Trait\TestsGetImport;

/**
 * Class ViewTest
 * @package RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Import
 */
class ViewTest extends \PHPUnit\Framework\TestCase
{
    protected Context $mockContext;
    protected Registry $mockRegistry;
    protected AuthorizationInterface $mockAuthorization;
    protected Import $mockImport;
    protected \Magento\Framework\UrlInterface $urlBuilder;
    protected ButtonList $mockButtonList;
    protected RequestInterface $mockRequest;

    protected function setUp(): void
    {
        $this->mockContext = $this->createMock(Context::class);
        $this->mockRegistry = $this->createMock(Registry::class);
        $this->mockAuthorization = $this->createMock(AuthorizationInterface::class);
        $this->mockImport = $this->createMock(Import::class);
        $this->urlBuilder = $this->createMock(\Magento\Framework\UrlInterface::class);
        $this->mockButtonList = $this->createMock(ButtonList::class);
        $this->mockRequest = $this->createMock(RequestInterface::class);

        $this->mockImport
            ->method('getRequestId')
            ->willReturn(1);

        $this->mockContext
            ->method('getUrlBuilder')
            ->willReturn($this->urlBuilder);

        $this->mockContext
            ->method('getAuthorization')
            ->willReturn($this->mockAuthorization);

        $this->mockContext
            ->method('getButtonList')
            ->willReturn($this->mockButtonList);

        $this->mockContext
            ->method('getRequest')
            ->willReturn($this->mockRequest);

        $this->mockImport
            ->method('getEntity')
            ->willReturn('Inventory');

        $this->mockAuthorization
            ->method('isAllowed')
            ->with('RealtimeDespatch_OrderFlow::orderflow_requests_imports')
            ->willReturn(true);
    }

    public function testConstruct(): void
    {
        $this->urlBuilder
            ->method('getUrl')
            ->willReturnCallback(function($route, $params) {
                if ($route == 'orderflow/import/index' && $params == ['request_id' => 1]) {
                    return 'http://localhost/admin/orderflow/imports/index';
                } else if ($route == 'orderflow/import/index/type/inventory') {
                    return 'http://localhost/admin/orderflow/import/index/type/inventory';
                } else if ($route == 'orderflow/request/view' && $params == ['request_id' => 1]) {
                    return 'http://localhost/admin/orderflow/request/view/1';
                }
                throw new \Exception('Invalid route: ' . $route);
            });

        $this->mockButtonList
            ->expects($this->exactly(3))
            ->method('remove')
            ->withConsecutive(
                ['delete'],
                ['reset'],
                ['save']
            );

        $block = new View(
            $this->mockContext,
            $this->mockRegistry,
            [
                'import' => $this->mockImport,
            ]
        );

        $this->assertEquals('orderflow_import_view', $block->getId());
    }

    /**
     * @dataProvider getImportDataProvider
     * @return void
     */
    public function testGetImport(bool $setData, ?string $registryKey): void
    {
        $data = ($setData) ? ['import' => $this->mockImport] : [];

        if ($registryKey) {
            $this->mockRegistry
                ->method('registry')
                ->willReturnCallback(function ($key) use ($registryKey) {
                    if ($key === $registryKey) {
                        return $this->mockImport;
                    }
                    return null;
                });
        }

        if (!$setData && !$registryKey) {
            $this->expectException(\Magento\Framework\Exception\LocalizedException::class);
            $this->expectExceptionMessage('Import Not Found.');
        }

        new View(
            $this->mockContext,
            $this->mockRegistry,
            $data
        );
    }

    public function getImportDataProvider(): array
    {
        return [
            [true, null],
            [false, 'current_import'],
            [false, 'import'],
            [false, null],
        ];
    }
}