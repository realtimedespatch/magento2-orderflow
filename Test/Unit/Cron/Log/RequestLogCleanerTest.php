<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Cron\Log;

use RealtimeDespatch\OrderFlow\Cron\Log\RequestLogCleaner;
use \RealtimeDespatch\OrderFlow\Helper\Log\Cleaning as CleaningHelper;
use RealtimeDespatch\OrderFlow\Model\Request;
use RealtimeDespatch\OrderFlow\Model\RequestFactory;
use Psr\Log\LoggerInterface;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Request\Collection;


class RequestLogCleanerTest extends \PHPUnit\Framework\TestCase
{
    protected RequestLogCleaner $importLogCleaner;
    protected LoggerInterface $mockLogger;
    protected RequestFactory $mockRequestFactory;
    protected Request $mockRequest;
    protected Collection $mockCollection;
    protected CleaningHelper $mockHelper;

    protected function setUp(): void
    {
        $this->mockLogger = $this->createMock(LoggerInterface::class);
        $this->mockRequestFactory = $this->createMock(RequestFactory::class);
        $this->mockRequest = $this->createMock(Request::class);
        $this->mockHelper = $this->createMock(CleaningHelper::class);
        $this->mockCollection = $this->createMock(Collection::class);

        $this->importLogCleaner = new RequestLogCleaner(
            $this->mockLogger,
            $this->mockRequestFactory,
            $this->mockHelper
        );
    }

    public function testExecuteDisable(): void
    {
        $this->mockHelper
            ->method('isEnabled')
            ->willReturn(false);

        $this->mockRequestFactory
            ->expects($this->never())
            ->method('create');

        $this->importLogCleaner->execute();
    }

    public function testExecute(): void
    {
        $this->mockHelper
            ->method('isEnabled')
            ->willReturn(true);

        $this->mockRequestFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->mockRequest);

        $this->mockRequest
            ->expects($this->once())
            ->method('getCollection')
            ->willReturn($this->mockCollection);

        $logDuration = rand(7, 365);
        $expectedDate = date('Y-m-d', strtotime("-". ($logDuration - 1) . " days"));

        $this->mockHelper
            ->method('getRequestLogDuration')
            ->willReturn($logDuration);

        $this->mockCollection
            ->expects($this->once())
            ->method('addFieldToFilter')
            ->with('created_at', ['lteq' => $expectedDate])
            ->willReturnSelf();

        $this->mockCollection
            ->expects($this->once())
            ->method('walk')
            ->with('delete');

        $this->importLogCleaner->execute();
    }
}