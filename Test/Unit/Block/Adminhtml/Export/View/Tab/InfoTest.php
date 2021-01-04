<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Export\View\Tab;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\Website;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Api\Data\ExportInterface;
use RealtimeDespatch\OrderFlow\Api\ExportRepositoryInterface;
use RealtimeDespatch\OrderFlow\Block\Adminhtml\Export\View\Tab\Info;
use Magento\Store\Model\WebsiteFactory;

class InfoTest extends TestCase
{
    /**
     * @var MockObject
     */
    protected $request;

    /**
     * @var MockObject
     */
    protected $websiteFactory;

    /**
     * @var MockObject
     */
    protected $exportRepository;

    /**
     * @var Info
     */
    protected $block;

    public function setUp()
    {
        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->request = $this->getMockBuilder(RequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->websiteFactory = $this->getMockBuilder(WebsiteFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->exportRepository = $this->getMockBuilder(ExportRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $data = [];

        $this->block = new Info(
            $context,
            $this->request,
            $this->websiteFactory,
            $this->exportRepository,
            $data
        );
    }

    public function testGetExportForAvailableExport()
    {
        $export = $this->getMockBuilder(ExportInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->block->setData('export', $export);

        $this->assertEquals($export, $this->block->getExport());
    }

    public function testGetExportForNullExport()
    {
        $exportId = 1;

        $this->request->expects($this->once())
            ->method('getParam')
            ->with('export_id')
            ->willReturn($exportId);

        $export = $this->getMockBuilder(ExportInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->exportRepository->expects($this->once())
            ->method('get')
            ->with($exportId)
            ->willReturn($export);

        $this->assertEquals($export, $this->block->getExport());
    }

    public function testGetWebsiteNameForNullScopeId()
    {
        $expectedValue = 'OrderFlow';

        $export = $this->getMockBuilder(ExportInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $export->expects($this->once())
            ->method('getScopeId')
            ->willReturn(null);

        $this->block->setData('export', $export);

        $this->assertEquals($expectedValue, $this->block->getWebsiteName());
    }

    public function testGetWebsiteNameForValidScopeId()
    {
        $websiteName = 'Website 1';
        $scopeId = 1;

        // Mock Export
        $export = $this->getMockBuilder(ExportInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $export->expects($this->any())
            ->method('getScopeId')
            ->willReturn($scopeId);

        $this->block->setData('export', $export);

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
