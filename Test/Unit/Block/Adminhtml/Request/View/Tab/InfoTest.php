<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Request\View\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Store\Model\Website;
use RealtimeDespatch\OrderFlow\Block\Adminhtml\Request\View\Tab\Info;
use RealtimeDespatch\OrderFlow\Model\Import;
use Magento\Store\Model\WebsiteFactory;
use RealtimeDespatch\OrderFlow\Model\Request;


/**
 * Class InfoTest
 * @package RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Request\View\Tab
 */
class InfoTest extends \PHPUnit\Framework\TestCase
{
    protected Info $block;
    protected Context $mockContext;
    protected Registry $mockRegistry;
    protected WebsiteFactory $mockWebsiteFactory;
    protected Website $mockWebsite;
    protected Request $mockRequest;

    protected function setUp(): void
    {
        $this->mockContext = $this->createMock(Context::class);
        $this->mockRegistry = $this->createMock(Registry::class);
        $this->mockWebsiteFactory = $this->createMock(WebsiteFactory::class);
        $this->mockRequest = $this->createMock(Request::class);
        $this->mockWebsite = $this->createMock(Website::class);

        $this->block = new Info(
            $this->mockContext,
            $this->mockRegistry,
            $this->mockWebsiteFactory,
            [
                'request' => $this->mockRequest
            ]
        );
    }

    public function testGetSource(): void
    {
        $this->assertEquals(
            $this->mockRequest,
            $this->block->getSource()
        );
    }

    public function testGetWebsiteName(): void
    {
        $this->mockWebsiteFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->mockWebsite);

        $this->mockRequest
            ->expects($this->exactly(2))
            ->method('getScopeId')
            ->willReturn(1);

        $this->mockWebsite
            ->expects($this->once())
            ->method('load')
            ->with(1)
            ->willReturnSelf();

        $this->mockWebsite
            ->expects($this->once())
            ->method('getName')
            ->willReturn('Main Website');

        $this->assertEquals(
            'Main Website',
            $this->block->getWebsiteName()
        );
    }

    public function testGetWebsiteNameNoScopeId(): void
    {
        $this->assertEquals(
            'OrderFlow',
            $this->block->getWebsiteName()
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
}