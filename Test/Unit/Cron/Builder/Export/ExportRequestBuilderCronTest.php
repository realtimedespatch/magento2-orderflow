<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Cron\Builder\Export;

use Magento\Store\Model\Website;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Api\ExportRequestBuilderInterface;
use Magento\Store\Model\ResourceModel\Website\CollectionFactory as WebsiteCollectionFactory;
use RealtimeDespatch\OrderFlow\Cron\Builder\Export\ExportRequestBuilderCron;

class ExportRequestBuilderCronTest extends TestCase
{
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
    protected $builderOne;

    /**
     * @var MockObject
     */
    protected $builderTwo;

    protected $cron;

    public function setUp()
    {
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

        $this->builderOne = $this->getMockBuilder(ExportRequestBuilderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->builderTwo = $this->getMockBuilder(ExportRequestBuilderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $builders = [$this->builderOne, $this->builderTwo];

        $this->cron = new ExportRequestBuilderCron($websiteCollectionFactory, $builders);
    }

    public function testExecute()
    {
        $this->builderOne->expects($this->exactly(2))
            ->method('build')
            ->withConsecutive($this->websiteOne, $this->websiteTwo);

        $this->builderTwo->expects($this->exactly(2))
            ->method('build')
            ->withConsecutive($this->websiteOne, $this->websiteTwo);

        $this->cron->execute();
    }
}
