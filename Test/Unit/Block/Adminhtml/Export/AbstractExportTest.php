<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Export;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Layout;
use Magento\Store\Model\WebsiteFactory;
use RealtimeDespatch\OrderFlow\Model\Export;

abstract class AbstractExportTest extends \PHPUnit\Framework\TestCase
{
    protected Context $mockContext;
    protected Registry $mockRegistry;
    protected WebsiteFactory $mockWebsiteFactory;
    protected Export $mockExport;
    protected LayoutInterface $mockLayout;

    protected function setUp(): void
    {
        $this->mockContext = $this->createMock(Context::class);
        $this->mockRegistry = $this->createMock(Registry::class);
        $this->mockWebsiteFactory = $this->createMock(WebsiteFactory::class);
        $this->mockExport = $this->createMock(Export::class);
        $this->mockLayout = $this->createMock(Layout::class);

        $this->mockContext
            ->method('getLayout')
            ->willReturn($this->mockLayout);
    }

    /**
     * @dataProvider getSourceDataProvider
     * @param bool $setData
     * @param string|null $registryKey
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function testGetSource(bool $setData, string|null $registryKey): void
    {
        if ($setData) {
            $this->block->setExport($this->mockExport);
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

        $result = $this->block->getSource();

        if ($setData || $registryKey) {
            $this->assertInstanceOf(\RealtimeDespatch\OrderFlow\Api\Data\ExportInterface::class, $result);
        }
    }

    public function getSourceDataProvider(): array
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