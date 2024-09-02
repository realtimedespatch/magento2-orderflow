<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Import\Trait;

use Magento\Framework\Exception\LocalizedException;
use RealtimeDespatch\OrderFlow\Model\Import;

trait TestsGetImport
{
    public function testGetImportData(): void
    {
        $mockImport = $this->createMock(Import::class);
        $this->block->setImport($mockImport);
        $this->assertEquals($mockImport, $this->block->getImport());
    }

    public function testGetImportRegistryCurrentImport(): void
    {
        $mockImport = $this->createMock(Import::class);
        $this->mockRegistry
            ->method('registry')
            ->willReturnCallback(function($key) use ($mockImport) {
                if ($key == 'current_import') {
                    return $mockImport;
                }
                return null;
            });
        $this->assertEquals($mockImport, $this->block->getImport());
    }

    public function testGetImportRegistryImport(): void
    {
        $mockImport = $this->createMock(Import::class);
        $this->mockRegistry
            ->method('registry')
            ->willReturnCallback(function($key) use ($mockImport) {
                if ($key == 'import') {
                    return $mockImport;
                }
                return null;
            });
        $this->assertEquals($mockImport, $this->block->getImport());
    }

    public function testGetImportException(): void
    {
        $this->expectException(LocalizedException::class);
        $this->block->getImport();
    }
}