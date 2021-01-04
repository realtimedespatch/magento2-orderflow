<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Request\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\Website;
use Magento\Store\Model\WebsiteFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Api\RequestRepositoryInterface;
use RealtimeDespatch\OrderFlow\Block\Adminhtml\Request\View\Tab\Info;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface as RtdRequest;

class InfoTest extends TestCase
{
    /**
     * @var MockObject
     */
    protected $request;

    /**
     * @var MockObject
     */
    protected $requestRepository;

    /**
     * @var MockObject
     */
    protected $websiteFactory;

    /**
     * @var Info
     */
    protected $block;

    public function setup()
    {
        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->request = $this->getMockBuilder(RequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestRepository = $this->getMockBuilder(RequestRepositoryInterface::class)
            ->getMock();

        $this->websiteFactory = $this->getMockBuilder(WebsiteFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $data = [];

        $this->block = new Info(
            $context,
            $this->request,
            $this->requestRepository,
            $this->websiteFactory,
            $data
        );
    }

    public function testGetWebsiteNameForNullScopeId()
    {
        $expectedValue = 'OrderFlow';

        $request = $this->getMockBuilder(RtdRequest::class)
            ->disableOriginalConstructor()
            ->getMock();

        $request->expects($this->once())
            ->method('getScopeId')
            ->willReturn(null);

        $this->block->setRtdRequest($request);

        $this->assertEquals($expectedValue, $this->block->getWebsiteName());
    }

    public function testGetWebsiteNameForValidScopeId()
    {
        $websiteName = 'Website 1';
        $scopeId = 1;

        // Mock Export
        $request = $this->getMockBuilder(RtdRequest::class)
            ->disableOriginalConstructor()
            ->getMock();

        $request->expects($this->any())
            ->method('getScopeId')
            ->willReturn($scopeId);

        $this->block->setRtdRequest($request);

        // Mock Website
        $website = $this->getMockBuilder(Website::class)
            ->disableOriginalConstructor()
            ->getMock();

        $website->expects($this->once())
            ->method('load')
            ->with($scopeId)
            ->willReturn($website);

        $website->expects($this->once())
            ->method('getName')
            ->willReturn($websiteName);

        $this->websiteFactory->expects($this->once())
            ->method('create')
            ->willReturn($website);

        $this->assertEquals($websiteName, $this->block->getWebsiteName());
    }

    public function testGetTabLabel()
    {
        $expectedLabel = __('Information');

        $this->assertEquals($expectedLabel, $this->block->getTabLabel());
    }

    public function testGetTabTitle()
    {
        $expectedTitle = __('Information');

        $this->assertEquals($expectedTitle, $this->block->getTabTitle());
    }

    public function testCanShowTab()
    {
        $this->assertTrue($this->block->canShowTab());
    }

    public function testIsHidden()
    {
        $this->assertFalse($this->block->isHidden());
    }

    public function testIsAjaxLoaded()
    {
        $this->assertFalse($this->block->isAjaxLoaded());
    }
}
