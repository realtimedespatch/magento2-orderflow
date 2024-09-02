<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Helper\Admin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Helper\Admin\Info;


class InfoTest extends TestCase
{
    protected Info $infoHelper;
    protected ScopeConfigInterface $mockScopeConfig;

    protected function setUp(): void
    {
        $mockContext = $this->createMock(\Magento\Framework\App\Helper\Context::class);

        $this->mockScopeConfig = $this->createMock(\Magento\Framework\App\Config\ScopeConfigInterface::class);
        $mockContext->method('getScopeConfig')->willReturn($this->mockScopeConfig);

        $this->infoHelper = new Info(
            $mockContext
        );

        parent::setUp();
    }

    /**
     * @dataProvider isEnabledDataProvider
     */
    public function testIsEnabled($enabled): void
    {
        $this->mockScopeConfig
            ->method('isSetFlag')
            ->with('orderflow_admin_info/settings/is_enabled')
            ->willReturn($enabled);

        $this->assertEquals($enabled, $this->infoHelper->isEnabled());
    }

    public function isEnabledDataProvider(): array
    {
        return [
            [true],
            [false],
        ];
    }
}