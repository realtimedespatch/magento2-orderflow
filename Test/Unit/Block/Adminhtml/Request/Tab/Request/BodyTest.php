<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Request\Tab\Request;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\WebsiteFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Api\RequestRepositoryInterface;
use RealtimeDespatch\OrderFlow\Block\Adminhtml\Request\View\Tab\Request\Body;
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
    protected $requestRepository;

    /**
     * @var MockObject
     */
    protected $websiteFactory;

    protected $block;

    public function setUp()
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

        $this->block = new Body(
            $context,
            $this->request,
            $this->requestRepository,
            $this->websiteFactory,
            $data
        );
    }

    public function testGetTabLabel()
    {
        $expectedLabel = __('Request Body');

        $this->assertEquals($expectedLabel, $this->block->getTabLabel());
    }

    public function testGetTabTitle()
    {
        $expectedTitle = __('Request Body');

        $this->assertEquals($expectedTitle, $this->block->getTabTitle());
    }

    public function testCanShowTab()
    {
        // Import Request
        $importRequest = $this->getMockBuilder(RtdRequest::class)
            ->disableOriginalConstructor()
            ->getMock();

        $importRequest->expects($this->once())->method('isImport')->willReturn(true);
        $importRequest->expects($this->never())->method('getOperation');
        $this->block->setRtdRequest($importRequest);

        $this->assertTrue($this->block->canShowTab());

        // Export Request & Export Operation
        $exportRequestWithExportOperation = $this->getMockBuilder(RtdRequest::class)
            ->disableOriginalConstructor()
            ->getMock();

        $exportRequestWithExportOperation->expects($this->once())->method('isImport')->willReturn(false);
        $exportRequestWithExportOperation->expects($this->once())->method('getOperation')->willReturn('Export');
        $this->block->setRtdRequest($exportRequestWithExportOperation);

        $this->assertTrue($this->block->canShowTab());

        // Export Request & Non Export Operation
        $exportRequestWithExportOperation = $this->getMockBuilder(RtdRequest::class)
            ->disableOriginalConstructor()
            ->getMock();

        $exportRequestWithExportOperation->expects($this->once())->method('isImport')->willReturn(false);
        $exportRequestWithExportOperation->expects($this->once())->method('getOperation')->willReturn('Create');
        $this->block->setRtdRequest($exportRequestWithExportOperation);

        $this->assertFalse($this->block->canShowTab());
    }

    public function testIsHidden()
    {
        // Import Request
        $importRequest = $this->getMockBuilder(RtdRequest::class)
            ->disableOriginalConstructor()
            ->getMock();

        $importRequest->expects($this->once())->method('isImport')->willReturn(true);
        $importRequest->expects($this->never())->method('getOperation');
        $this->block->setRtdRequest($importRequest);

        $this->assertFalse($this->block->isHidden());

        // Export Request & Export Operation
        $exportRequestWithExportOperation = $this->getMockBuilder(RtdRequest::class)
            ->disableOriginalConstructor()
            ->getMock();

        $exportRequestWithExportOperation->expects($this->once())->method('isImport')->willReturn(false);
        $exportRequestWithExportOperation->expects($this->once())->method('getOperation')->willReturn('Export');
        $this->block->setRtdRequest($exportRequestWithExportOperation);

        $this->assertFalse($this->block->isHidden());

        // Export Request & Non Export Operation
        $exportRequestWithExportOperation = $this->getMockBuilder(RtdRequest::class)
            ->disableOriginalConstructor()
            ->getMock();

        $exportRequestWithExportOperation->expects($this->once())->method('isImport')->willReturn(false);
        $exportRequestWithExportOperation->expects($this->once())->method('getOperation')->willReturn('Create');
        $this->block->setRtdRequest($exportRequestWithExportOperation);

        $this->assertTrue($this->block->isHidden());
    }

    public function testIsAjaxLoaded()
    {
        $this->assertFalse($this->block->isAjaxLoaded());
    }
}
