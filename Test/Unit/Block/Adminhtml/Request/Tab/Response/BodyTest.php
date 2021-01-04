<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Request\Tab\Response;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\WebsiteFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Api\RequestRepositoryInterface;
use RealtimeDespatch\OrderFlow\Block\Adminhtml\Request\View\Tab\Response\Body;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface as RtdRequest;

class BodyTest extends TestCase
{
    /**
     * @var MockObject
     */
    protected $request;

    /**
     * @var MockObject
     */
    protected $responseRepository;

    /**
     * @var MockObject
     */
    protected $websiteFactory;

    /**
     * @var Body
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

        $this->responseRepository = $this->getMockBuilder(RequestRepositoryInterface::class)
            ->getMock();

        $this->websiteFactory = $this->getMockBuilder(WebsiteFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $data = [];

        $this->block = new Body(
            $context,
            $this->request,
            $this->responseRepository,
            $this->websiteFactory,
            $data
        );
    }

    public function testGetTabLabel()
    {
        $expectedLabel = __('Response Body');

        $this->assertEquals($expectedLabel, $this->block->getTabLabel());
    }

    public function testGetTabTitle()
    {
        $expectedTitle = __('Response Body');

        $this->assertEquals($expectedTitle, $this->block->getTabTitle());
    }

    public function testCanShowTab()
    {
        // Import Response
        $importResponse = $this->getMockBuilder(RtdRequest::class)
            ->disableOriginalConstructor()
            ->getMock();

        $importResponse->expects($this->once())->method('isImport')->willReturn(true);
        $importResponse->expects($this->never())->method('getOperation');
        $this->block->setRtdRequest($importResponse);

        $this->assertTrue($this->block->canShowTab());

        // Export Response & Export Operation
        $exportResponseWithExportOperation = $this->getMockBuilder(RtdRequest::class)
            ->disableOriginalConstructor()
            ->getMock();

        $exportResponseWithExportOperation->expects($this->once())->method('isImport')->willReturn(false);
        $exportResponseWithExportOperation->expects($this->once())->method('getOperation')->willReturn('Export');
        $this->block->setRtdRequest($exportResponseWithExportOperation);

        $this->assertTrue($this->block->canShowTab());

        // Export Response & Non Export Operation
        $exportResponseWithExportOperation = $this->getMockBuilder(RtdRequest::class)
            ->disableOriginalConstructor()
            ->getMock();

        $exportResponseWithExportOperation->expects($this->once())->method('isImport')->willReturn(false);
        $exportResponseWithExportOperation->expects($this->once())->method('getOperation')->willReturn('Create');
        $this->block->setRtdRequest($exportResponseWithExportOperation);

        $this->assertFalse($this->block->canShowTab());
    }

    public function testIsHidden()
    {
        // Import Response
        $importResponse = $this->getMockBuilder(RtdRequest::class)
            ->disableOriginalConstructor()
            ->getMock();

        $importResponse->expects($this->once())->method('isImport')->willReturn(true);
        $importResponse->expects($this->never())->method('getOperation');
        $this->block->setRtdRequest($importResponse);

        $this->assertFalse($this->block->isHidden());

        // Export Response & Export Operation
        $exportResponseWithExportOperation = $this->getMockBuilder(RtdRequest::class)
            ->disableOriginalConstructor()
            ->getMock();

        $exportResponseWithExportOperation->expects($this->once())->method('isImport')->willReturn(false);
        $exportResponseWithExportOperation->expects($this->once())->method('getOperation')->willReturn('Export');
        $this->block->setRtdRequest($exportResponseWithExportOperation);

        $this->assertFalse($this->block->isHidden());

        // Export Response & Non Export Operation
        $exportResponseWithExportOperation = $this->getMockBuilder(RtdRequest::class)
            ->disableOriginalConstructor()
            ->getMock();

        $exportResponseWithExportOperation->expects($this->once())->method('isImport')->willReturn(false);
        $exportResponseWithExportOperation->expects($this->once())->method('getOperation')->willReturn('Create');
        $this->block->setRtdRequest($exportResponseWithExportOperation);

        $this->assertTrue($this->block->isHidden());
    }

    public function testIsAjaxLoaded()
    {
        $this->assertFalse($this->block->isAjaxLoaded());
    }
}
