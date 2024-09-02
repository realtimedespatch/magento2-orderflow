<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\Service\Request;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\ManagerInterface;
use Psr\Log\LoggerInterface;
use RealtimeDespatch\OrderFlow\Api\RequestProcessorTypeInterface;
use RealtimeDespatch\OrderFlow\Model\Service\Request\RequestProcessor;

class RequestProcessorTest extends \PHPUnit\Framework\TestCase
{
    protected ScopeConfigInterface $mockScopeConfig;
    protected LoggerInterface $mockLogger;
    protected ManagerInterface $mockEventManager;
    protected RequestProcessor $requestProcessor;
    protected RequestProcessorTypeInterface $mockRequestProcessorType;

    protected function setUp(): void
    {
        $this->mockScopeConfig = $this->createMock(\Magento\Framework\App\Config\ScopeConfigInterface::class);
        $this->mockLogger = $this->createMock(\Psr\Log\LoggerInterface::class);
        $this->mockEventManager = $this->createMock(\Magento\Framework\Event\ManagerInterface::class);
        $this->mockRequestProcessorType = $this->getMockBuilder(\RealtimeDespatch\OrderFlow\Api\RequestProcessorTypeInterface::class)
            ->disableOriginalConstructor()
            ->addMethods(['canProcess'])
            ->onlyMethods(['process'])
            ->getMock();

        $this->requestProcessor = new RequestProcessor(
            $this->mockRequestProcessorType,
            $this->mockLogger,
            $this->mockEventManager
        );
    }

    public function testProcess(): void
    {
        $this->mockRequestProcessorType
            ->method('process')
            ->willReturn(true);

        $mockRequest = $this->createMock(\RealtimeDespatch\OrderFlow\Model\Request::class);
        $mockRequest->method('canProcess')->willReturn(true);

        $result = $this->requestProcessor->process($mockRequest);
        $this->assertTrue($result);
    }

    public function testCannotProcess(): void
    {
        $mockRequest = $this->createMock(\RealtimeDespatch\OrderFlow\Model\Request::class);
        $mockRequest->method('canProcess')->willReturn(false);

        $this->mockRequestProcessorType
            ->expects($this->never())
            ->method('process');

        $result = $this->requestProcessor->process($mockRequest);
        $this->assertFalse($result);
    }
}