<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Request;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\RequestInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface as RtdRequest;
use RealtimeDespatch\OrderFlow\Api\RequestRepositoryInterface;
use RealtimeDespatch\OrderFlow\Block\Adminhtml\Request\AbstractRequest;
use Magento\Store\Model\WebsiteFactory;

class AbstractRequestTest extends TestCase
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
     * @var AbstractRequest|__anonymous@925
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

        $this->requestRepository = $this->getMockBuilder(RequestRepositoryInterface::class)
            ->getMock();

        $websiteFactory = $this->getMockBuilder(WebsiteFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $data = [];

        $this->block = new class (
            $context,
            $this->request,
            $this->requestRepository,
            $websiteFactory,
            $data) extends AbstractRequest {};
    }

    public function testSetAndGetRequestWithValidRequest()
    {
        $request = $this->getMockBuilder(RtdRequest::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->block->setRtdRequest($request);

        $this->assertSame($request, $this->block->getRtdRequest());
    }

    public function testSetAndGetRequestWithNullRequest()
    {
        $requestId = 1;

        $request = $this->getMockBuilder(RtdRequest::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->request->expects($this->once())
            ->method('getParam')
            ->with('request_id')
            ->willReturn($requestId);

        $this->requestRepository->expects($this->once())
            ->method('get')
            ->with($requestId)
            ->willReturn($request);

        $this->assertSame($request, $this->block->getRtdRequest());
    }
}
