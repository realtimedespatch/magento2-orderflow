<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Export;

use Magento\Framework\Exception\LocalizedException;
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

    protected function setUp(): void
    {
        $this->mockContext = $this->createMock(Context::class);
        $this->mockRegistry = $this->createMock(Registry::class);
        $this->mockExport = $this->createMock(Export::class);


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
        $this->block = new View(
            $this->mockContext,
            $this->mockRegistry
        );

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
            $this->expectExceptionMessage('Export Not Found.');
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
}