<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Cron\Import;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use RealtimeDespatch\OrderFlow\Api\ImportHelperInterface;
use RealtimeDespatch\OrderFlow\Api\RequestProcessorFactoryInterface;
use RealtimeDespatch\OrderFlow\Cron\Import\ImportCron;
use RealtimeDespatch\OrderFlow\Model\Service\Request\RequestProcessor;

class ImportCronTest extends TestCase
{
    /**
     * @var MockObject
     */
    protected $helper;

    /**
     * @var MockObject
     */
    protected $reqProcessorFactory;

    /**
     * @var ImportCron
     */
    protected $cron;

    public function setUp()
    {
        $this->helper = $this->getMockBuilder(ImportHelperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->reqProcessorFactory = $this->getMockBuilder(RequestProcessorFactoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->cron = new ImportCron(
            $this->helper,
            $this->reqProcessorFactory
        );
    }

    public function testExecute()
    {
        $this->helper->expects($this->exactly(2))
            ->method('isEnabled')
            ->will($this->onConsecutiveCalls(false, true));

        $requestOne = $this->getMockBuilder(RequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $requestTwo = $this->getMockBuilder(RequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $importableRequests = [$requestOne, $requestTwo];

        $this->helper->expects($this->once())
            ->method('getImportableRequests')
            ->willReturn($importableRequests);

        $requestProcessor = $this->getMockBuilder(RequestProcessor::class)
            ->disableOriginalConstructor()
            ->getMock();

        $requestProcessor->expects($this->exactly(2))
            ->method('process')
            ->withConsecutive($requestOne, $requestTwo);

        $this->reqProcessorFactory->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive($requestOne, $requestTwo)
            ->willReturn($requestProcessor);

        // Call 1 - Exports Disabled
        $this->cron->execute();

        // Call 2 - Exports Enabled
        $this->cron->execute();
    }
}
