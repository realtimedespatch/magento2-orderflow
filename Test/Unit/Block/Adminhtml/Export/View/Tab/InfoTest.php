<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Export\View\Tab;

use Magento\Store\Model\Website;
use RealtimeDespatch\OrderFlow\Block\Adminhtml\Export\View\Tab\Info;
use RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Export\AbstractExportTest;

class InfoTest extends AbstractExportTest
{
    protected Info $block;
    protected Website $mockWebsite;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockWebsite = $this->createMock(Website::class);

        $this->block = new Info(
            $this->mockContext,
            $this->mockRegistry,
            $this->mockWebsiteFactory
        );
    }

    public function testGetTabLabel(): void
    {
        $this->assertEquals('Information', $this->block->getTabLabel());
    }

    public function testGetTabTitle(): void
    {
        $this->assertEquals('Information', $this->block->getTabTitle());
    }

    public function testCanShowTab(): void
    {
        $this->assertTrue($this->block->canShowTab());
    }

    public function testIsHidden(): void
    {
        $this->assertFalse($this->block->isHidden());
    }

    /**
     * @dataProvider getWebsiteNameProvider
     * @return void
     */
    public function testGetWebsiteName(int $scopeId, string $expectedName): void
    {

        $this->mockRegistry
            ->method('registry')
            ->with('current_export')
            ->willReturn($this->mockExport);

        if ($scopeId) {
            $this->mockExport
                ->method('getScopeId')
                ->willReturn($scopeId);

            $this->mockWebsiteFactory
                ->method('create')
                ->willReturn($this->mockWebsite);

            $this->mockWebsite
                ->method('load')
                ->with($scopeId)
                ->willReturnSelf();

            $this->mockWebsite
                ->method('getName')
                ->willReturn('Main Website');
        }

        $result = $this->block->getWebsiteName();
        $this->assertEquals($expectedName, $result);
    }

    public function getWebsiteNameProvider(): array
    {
        return [
            ['scopeId' => 0, 'expectedName' => 'OrderFlow'],
            ['scopeId' => 1, 'expectedName' => 'Main Website'],
        ];
    }
}