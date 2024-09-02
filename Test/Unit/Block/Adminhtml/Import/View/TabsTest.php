<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Import\View;

use RealtimeDespatch\OrderFlow\Block\Adminhtml\Import\View\Tabs;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Registry;
use Magento\Backend\Model\Auth\Session;
use RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Import\Trait\TestsGetImport;

/**
 * Class TabsTest
 * @package RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Import\View
 */
class TabsTest extends \PHPUnit\Framework\TestCase
{
    use TestsGetImport;

    protected Tabs $block;
    protected Context $mockContext;
    protected Registry $mockRegistry;
    protected EncoderInterface $mockJsonEncoder;
    protected Session $mockSession;

    protected function setUp(): void
    {
        $this->mockContext = $this->createMock(Context::class);
        $this->mockRegistry = $this->createMock(Registry::class);
        $this->mockJsonEncoder = $this->createMock(EncoderInterface::class);
        $this->mockSession = $this->createMock(Session::class);

        $this->block = new Tabs(
            $this->mockContext,
            $this->mockJsonEncoder,
            $this->mockSession,
            $this->mockRegistry,
        );
    }

    public function testConstruct(): void
    {
        $this->assertEquals('orderflow_import_view_tabs', $this->block->getId());
        $this->assertEquals('orderflow_import_view', $this->block->getDestElementId());
        $this->assertEquals(__('Import View'), $this->block->getTitle());
    }
}