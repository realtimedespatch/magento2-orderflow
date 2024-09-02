<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Export;

use Magento\Backend\Block\Widget\Button\ButtonList;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use RealtimeDespatch\OrderFlow\Block\Adminhtml\Export\View;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;
use RealtimeDespatch\OrderFlow\Model\Export;

class ViewTest extends \PHPUnit\Framework\TestCase
{
    protected View $block;
    protected Context $mockContext;
    protected Registry $mockRegistry;
    protected Export $mockExport;
    protected UrlInterface $mockUrl;
    protected ButtonList $mockButtonList;
    protected RequestInterface $mockRequest;
    protected \Magento\Framework\AuthorizationInterface $mockAuthorization;

    protected function setUp(): void
    {
        $this->mockContext = $this->createMock(Context::class);
        $this->mockRegistry = $this->createMock(Registry::class);
        $this->mockExport = $this->createMock(Export::class);
        $this->mockUrl = $this->createMock(UrlInterface::class);
        $this->mockButtonList = $this->createMock(ButtonList::class);
        $this->mockRequest = $this->createMock(RequestInterface::class);
        $this->mockAuthorization = $this->createMock(\Magento\Framework\AuthorizationInterface::class);

        $this->mockExport
            ->method('getEntity')
            ->willReturn('Order');

        $this->mockExport
            ->method('getRequestId')
            ->willReturn(1);

        $this->mockContext
            ->method('getUrlBuilder')
            ->willReturn($this->mockUrl);

        $this->mockContext
            ->method('getButtonList')
            ->willReturn($this->mockButtonList);

        $this->mockContext
            ->method('getAuthorization')
            ->willReturn($this->mockAuthorization);

        $this->mockContext
            ->method('getRequest')
            ->willReturn($this->mockRequest);
    }

    /**
     * @dataProvider getExportDataProvider
     * @param bool $setData
     * @param string|null $registryKey
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function testGetExport(bool $setData, string|null $registryKey): void
    {
        $data = [];
        if ($setData) {
            $data = ['export' => $this->mockExport];
        } else if ($registryKey) {
            $this->mockRegistry
                ->method('registry')
                ->willReturnCallback(function ($key) use ($registryKey) {
                    if ($key === $registryKey) {
                        return $this->mockExport;
                    }
                    return null;
                });
        } else {
            $this->expectException(LocalizedException::class);
            $this->expectExceptionMessage('Export Not Found.');
        }

        $this->block = new View(
            $this->mockContext,
            $this->mockRegistry,
            $data
        );

        $result = $this->block->getExport();

        if ($setData || $registryKey) {
            $this->assertInstanceOf(\RealtimeDespatch\OrderFlow\Api\Data\ExportInterface::class, $result);
        }
    }

    public function testCanViewRequest(): void
    {
        $this->mockAuthorization
            ->method('isAllowed')
            ->with('RealtimeDespatch_OrderFlow::orderflow_requests_exports')
            ->willReturn(true);

        $this->block = new View(
            $this->mockContext,
            $this->mockRegistry,
            ['export' => $this->mockExport]
        );

        $this->assertTrue($this->block->canViewRequest());

    }
    public function getExportDataProvider(): array
    {
        return [
            [
                'setData' => true,
                'registry_key' => null,
            ],
            [
                'setData' => false,
                'registry_key' => 'current_export',
            ],
            [
                'setData' => false,
                'registry_key' => 'export',
            ],
            [
                'setData' => false,
                'registry_key' => null,
            ]
        ];
    }
}