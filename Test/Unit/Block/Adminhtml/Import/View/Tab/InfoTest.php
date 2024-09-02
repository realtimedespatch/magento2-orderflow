<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Import\View\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use RealtimeDespatch\OrderFlow\Block\Adminhtml\Import\View\Tab\Info;
use RealtimeDespatch\OrderFlow\Model\Import;
use RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Import\Trait\TestsGetImport;

/**
 * Class InfoTest
 * @package RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Import\View\Tab
 */
class InfoTest extends \PHPUnit\Framework\TestCase
{
    use TestsGetImport;

    protected Info $block;
    protected Context $mockContext;
    protected Registry $mockRegistry;

    protected function setUp(): void
    {
        $this->mockContext = $this->createMock(Context::class);
        $this->mockRegistry = $this->createMock(Registry::class);

        $this->block = new Info(
            $this->mockContext,
            $this->mockRegistry
        );
    }

    public function testGetSource(): void
    {
        $mockImport = $this->createMock(Import::class);
        $this->block->setImport($mockImport);
        $this->assertEquals($mockImport, $this->block->getSource());
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
}