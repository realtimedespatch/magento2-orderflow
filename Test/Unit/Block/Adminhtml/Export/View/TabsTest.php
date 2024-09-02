<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Export\View;

use Magento\Framework\Exception\LocalizedException;
use RealtimeDespatch\OrderFlow\Block\Adminhtml\Export\View\Tabs;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Json\EncoderInterface;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Registry;
use RealtimeDespatch\OrderFlow\Model\Export;

class TabsTest extends \PHPUnit\Framework\TestCase
{
    protected Tabs $block;
    protected Context $mockContext;
    protected EncoderInterface $mockJsonEncoder;
    protected Session $mockAuthSession;
    protected Registry $mockRegistry;
    protected Export $mockExport;

    protected function setUp(): void
    {
        $this->mockContext = $this->createMock(Context::class);
        $this->mockJsonEncoder = $this->createMock(EncoderInterface::class);
        $this->mockAuthSession = $this->createMock(Session::class);
        $this->mockRegistry = $this->createMock(Registry::class);
        $this->mockExport = $this->createMock(Export::class);

        $this->block = new Tabs(
            $this->mockContext,
            $this->mockJsonEncoder,
            $this->mockAuthSession,
            $this->mockRegistry,
            []
        );
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
        if ($setData) {
            $this->block->setData('export', $this->mockExport);
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
            $this->expectExceptionMessage('We can\'t get the export instance right now.');
        }

        $result = $this->block->getExport();

        if ($setData || $registryKey) {
            $this->assertInstanceOf(\RealtimeDespatch\OrderFlow\Api\Data\ExportInterface::class, $result);
        }
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

    public function testConstruct(): void
    {
        $this->assertEquals('orderflow_export_view_tabs', $this->block->getId());
        $this->assertEquals('orderflow_export_view', $this->block->getDestElementId());
        $this->assertEquals('Export View', $this->block->getTitle());
    }
}