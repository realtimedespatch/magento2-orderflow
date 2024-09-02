<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Helper\Log;

use Magento\Framework\App\Config\ScopeConfigInterface;
use RealtimeDespatch\OrderFlow\Helper\Log\Cleaning;

class CleaningTest extends \PHPUnit\Framework\TestCase
{
    protected Cleaning $cleaningHelper;
    protected ScopeConfigInterface $mockScopeConfig;

    protected function setUp(): void
    {
        $mockContext = $this->createMock(\Magento\Framework\App\Helper\Context::class);

        $this->mockScopeConfig = $this->createMock(\Magento\Framework\App\Config\ScopeConfigInterface::class);
        $mockContext->method('getScopeConfig')->willReturn($this->mockScopeConfig);

        $this->cleaningHelper = new Cleaning(
            $mockContext
        );

        parent::setUp();
    }

    public function testIsEnabled(): void
    {
        $this->mockScopeConfig
            ->method('isSetFlag')
            ->with('orderflow_log_cleaning/settings/is_enabled')
            ->willReturn(true);

        $this->assertEquals(true, $this->cleaningHelper->isEnabled());
    }

    public function testGetExportLogDuration(): void
    {
        $retention = rand(1, 100);

        $this->mockScopeConfig
            ->method('getValue')
            ->with('orderflow_log_cleaning/settings/export_duration')
            ->willReturn($retention);

        $this->assertEquals($retention, $this->cleaningHelper->getExportLogDuration());
    }

    public function testGetImportLogDuration(): void
    {
        $retention = rand(1, 100);

        $this->mockScopeConfig
            ->method('getValue')
            ->with('orderflow_log_cleaning/settings/import_duration')
            ->willReturn($retention);

        $this->assertEquals($retention, $this->cleaningHelper->getImportLogDuration());
    }

    public function testGetRequestLogDuration(): void
    {
        $retention = rand(1, 100);

        $this->mockScopeConfig
            ->method('getValue')
            ->with('orderflow_log_cleaning/settings/request_duration')
            ->willReturn($retention);

        $this->assertEquals($retention, $this->cleaningHelper->getRequestLogDuration());
    }

}

