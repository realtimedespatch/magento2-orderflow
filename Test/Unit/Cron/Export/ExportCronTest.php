<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Cron\Export;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use RealtimeDespatch\OrderFlow\Api\ExportHelperInterface;
use RealtimeDespatch\OrderFlow\Api\RequestProcessorFactoryInterface;
use RealtimeDespatch\OrderFlow\Cron\Export\ExportCron;
use RealtimeDespatch\OrderFlow\Model\Service\Request\RequestProcessor;
use Magento\Store\Model\Website;
use Magento\Store\Model\ResourceModel\Website\CollectionFactory as WebsiteCollectionFactory;

class ExportCronTest extends TestCase
{
    /**
     * @var MockObject
     */
    protected $helper;

    /**
     * @var MockObject
     */
    protected $websiteOne;

    /**
     * @var MockObject
     */
    protected $websiteTwo;

    /**
     * @var MockObject
     */
    protected $reqProcessorFactory;

    /**
     * @var ExportCron
     */
    protected $cron;

    public function setUp()
    {
        $this->helper = $this->getMockBuilder(ExportHelperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->reqProcessorFactory = $this->getMockBuilder(RequestProcessorFactoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->websiteOne = $this->getMockBuilder(Website::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->websiteTwo = $this->getMockBuilder(Website::class)
            ->disableOriginalConstructor()
            ->getMock();

        $websites = [$this->websiteOne, $this->websiteTwo];

        $websiteCollectionFactory = $this->getMockBuilder(WebsiteCollectionFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $websiteCollectionFactory->expects($this->once())
            ->method('create')
            ->willReturn($websites);

        $this->cron = new ExportCron(
            $this->helper,
            $websiteCollectionFactory,
            $this->reqProcessorFactory
        );
    }

    public function testExecute()
    {
        $websiteOneId = 111;
        $websiteTwoId = 222;

        $this->websiteOne->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($websiteOneId);

        $this->websiteTwo->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($websiteTwoId);

        $this->helper->expects($this->exactly(4))
            ->method('isEnabled')
            ->with()
            ->will($this->onConsecutiveCalls(true, true, false, false));

        $requestOne = $this->getMockBuilder(RequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $requestTwo = $this->getMockBuilder(RequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $exportableRequests = [$requestOne, $requestTwo];

        $this->helper->expects($this->exactly(2))
            ->method('getExportableRequests')
            ->willReturn($exportableRequests);

        $requestProcessor = $this->getMockBuilder(RequestProcessor::class)
            ->disableOriginalConstructor()
            ->getMock();

        $requestProcessor->expects($this->exactly(4))
            ->method('process')
            ->withConsecutive($requestOne, $requestTwo, $requestOne, $requestTwo);

        $this->reqProcessorFactory->expects($this->exactly(4))
            ->method('get')
            ->withConsecutive($requestOne, $requestTwo, $requestOne, $requestTwo)
            ->willReturn($requestProcessor);

        // Call 1 - Exports Disabled
        $this->cron->execute();

        // Call 2 - Exports Enabled
        $this->cron->execute();
    }
}
