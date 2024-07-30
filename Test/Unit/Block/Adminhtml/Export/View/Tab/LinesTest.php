<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Export\View\Tab;

use Magento\Framework\Exception\LocalizedException;
use RealtimeDespatch\OrderFlow\Block\Adminhtml\Export\View\Tab\Lines;
use RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Export\AbstractExportTest;

class LinesTest extends AbstractExportTest
{
    protected Lines $block;

    protected function setUp(): void
    {
        parent::setUp();

        $this->block = new Lines(
            $this->mockContext,
            $this->mockRegistry,
            $this->mockWebsiteFactory
        );
    }

    public function testGetTabLabel(): void
    {
        $this->assertEquals('Export Lines', $this->block->getTabLabel());
    }

    public function testGetTabTitle(): void
    {
        $this->assertEquals('Export Lines', $this->block->getTabTitle());
    }

    public function testCanShowTab(): void
    {
        $this->assertTrue($this->block->canShowTab());
    }

    public function testIsHidden(): void
    {
        $this->assertFalse($this->block->isHidden());
    }

    public function testGetItemsHtml(): void
    {
        $this->block->setNameInLayout('lines');

        $this->mockLayout
            ->expects($this->once())
            ->method('getChildName')
            ->with('lines')
            ->willReturn('items');

        $this->mockLayout
            ->expects($this->once())
            ->method('renderElement')
            ->with('items', true)
            ->willReturn('Items');

        $this->assertEquals('Items', $this->block->getItemsHtml());
    }
}