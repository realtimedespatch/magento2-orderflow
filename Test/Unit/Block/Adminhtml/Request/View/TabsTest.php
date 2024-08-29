<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Request\View;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\EncoderInterface;
use RealtimeDespatch\OrderFlow\Block\Adminhtml\Request\View\Tabs;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Backend\Model\Auth\Session;
use RealtimeDespatch\OrderFlow\Model\Request;


/**
 * Class TabsTest
 * @package RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Request\View
 */
class TabsTest extends \PHPUnit\Framework\TestCase
{
    protected Tabs $block;
    protected Context $mockContext;
    protected Registry $mockRegistry;
    protected EncoderInterface $mockJsonEncoder;
    protected Session $mockSession;
    protected Request $mockRequest;

    protected function setUp(): void
    {
        $this->mockContext = $this->createMock(Context::class);
        $this->mockRegistry = $this->createMock(Registry::class);
        $this->mockJsonEncoder = $this->createMock(EncoderInterface::class);
        $this->mockSession = $this->createMock(Session::class);
        $this->mockRequest = $this->createMock(Request::class);

        $this->block = new Tabs(
            $this->mockContext,
            $this->mockJsonEncoder,
            $this->mockSession,
            $this->mockRegistry
        );
    }

    public function testConstruct(): void
    {
        $this->assertEquals('orderflow_request_view_tabs', $this->block->getId());
        $this->assertEquals('orderflow_request_view', $this->block->getDestElementId());
        $this->assertEquals(__('Request View'), $this->block->getTitle());
    }

    public function testGetRequestData(): void
    {
        $this->block->setData('request', $this->mockRequest);
        $this->assertEquals($this->mockRequest, $this->block->getRequest());
    }

    public function testGetRequestDataRegistryCurrentRequest(): void
    {
        $this->mockRegistry
            ->expects($this->exactly(2))
            ->method('registry')
            ->with('current_request')
            ->willReturn($this->mockRequest);

        $this->assertEquals($this->mockRequest, $this->block->getRequest());
    }

    public function testGetRequestDataRegistryRequest(): void
    {
        $this->mockRegistry
            ->method('registry')
            ->withConsecutive(
                ['current_request'],
                ['request'],
                ['request']
            )
            ->willReturnOnConsecutiveCalls(
                null,
                $this->mockRequest,
                $this->mockRequest,
            );

        $this->assertEquals($this->mockRequest, $this->block->getRequest());
    }

    public function testGetRequestDataRegistryException(): void
    {
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Request Not Found.');
        $this->block->getRequest();
    }
}