<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Request\View\Tab;

use Magento\Backend\Block\Template\Context;
use RealtimeDespatch\OrderFlow\Block\Adminhtml\Request\View\Tab\Lines;

/**
 * Class LinesTest
 * @package RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Request\View\Tab
 */
class LinesTest extends \PHPUnit\Framework\TestCase
{
    protected Lines $block;
    protected Context $mockContext;

    protected function setUp(): void
    {
        $this->mockContext = $this->createMock(Context::class);

        $this->block = new Lines(
            $this->mockContext,
            [],
        );
    }

    public function testGetTabLabel(): void
    {
        $this->assertEquals('Request Lines', $this->block->getTabLabel());
    }

    public function testGetTabTitle(): void
    {
        $this->assertEquals('Request Lines', $this->block->getTabTitle());
    }

    public function testCanShowTab(): void
    {
        $this->assertTrue($this->block->canShowTab());
    }

    public function testIsHidden(): void
    {
        $this->assertFalse($this->block->isHidden());
    }
}