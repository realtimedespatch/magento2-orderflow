<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Request\View\Tab\Request;

use RealtimeDespatch\OrderFlow\Block\Adminhtml\Request\View\Tab\Request\Body;
use RealtimeDespatch\OrderFlow\Model\Request;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Store\Model\Website;
use Magento\Store\Model\WebsiteFactory;
use RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Request\AbstractRequest;

/**
 * Class BodyTest
 * @package RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Request\View\Tab\Request
 */
class BodyTest extends AbstractRequest
{
    protected Context $mockContext;
    protected Registry $mockRegistry;
    protected WebsiteFactory $mockWebsiteFactory;
    protected Website $mockWebsite;

    protected function setUp(): void
    {
        $this->mockContext = $this->createMock(Context::class);
        $this->mockRegistry = $this->createMock(Registry::class);
        $this->mockWebsiteFactory = $this->createMock(WebsiteFactory::class);
        $this->mockWebsite = $this->createMock(Website::class);
        $this->mockRequest = $this->createMock(Request::class);

        $this->block = new Body(
            $this->mockContext,
            $this->mockRegistry,
            $this->mockWebsiteFactory,
            [
                'request' => $this->mockRequest
            ]
        );
    }

    public function testGetTabLabel(): void
    {
        $this->assertEquals('Request Body', $this->block->getTabLabel());
    }

    public function testGetTabTitle(): void
    {
        $this->assertEquals('Request Body', $this->block->getTabTitle());
    }

    public function testCanShowTab(): void
    {
        $this->mockRequest
            ->expects($this->once())
            ->method('isExport')
            ->willReturn(false);

        $this->assertTrue($this->block->canShowTab());
    }

    public function testIsHidden(): void
    {
        $this->mockRequest
            ->expects($this->exactly(2))
            ->method('isExport')
            ->willReturnOnConsecutiveCalls(true, false);

        $this->mockRequest
            ->expects($this->exactly(1))
            ->method('getOperation')
            ->willReturn('Import', 'Export');

        $this->assertTrue($this->block->isHidden());
        $this->assertFalse($this->block->isHidden());
    }

    public function testGetSource(): void
    {
        $this->assertEquals($this->mockRequest, $this->block->getSource());
    }
}