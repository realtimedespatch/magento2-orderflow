<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\Config\Backend;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Model\Config\Backend\Cron;

class CronTest extends TestCase
{
    /**
     * @var Cron
     */
    protected $cron;

    public function setUp()
    {
        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $eventManager = $this->getMockBuilder(ManagerInterface::class)->getMock();
        $eventManager->expects($this->any())->method('dispatch');

        $context->expects($this->once())
            ->method('getEventDispatcher')
            ->willReturn($eventManager);

        $registry = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->getMock();

        $config = $this->getMockBuilder(ScopeConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $cacheTypeList = $this->getMockBuilder(TypeListInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resource = $this->getMockBuilder(AbstractResource::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resourceCollection = $this->getMockBuilder(AbstractDb::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->cron = new Cron(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $resource,
            $resourceCollection
        );
    }

    public function testBeforeSaveWithValidCronExpression()
    {
        $this->cron->setData('value', '* * * * *');
        $this->assertEquals($this->cron, $this->cron->beforeSave());
    }

    public function testBeforeSaveWithInvalidCronExpression()
    {
        try{
            $this->cron->setData('value', '* * * * * 10');
            $this->cron->beforeSave();
            $this->fail("Expected exception not thrown");
        } catch(LocalizedException $e) {
            $this->assertEquals(__('Unable to parse the cron expression.'), $e->getMessage());
        }
    }
}
