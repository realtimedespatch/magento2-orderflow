<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Ui\Listing\Columns;

use DateTime;
use Magento\Framework\Locale\Bundle\DataBundle;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Stdlib\BooleanUtils;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Ui\Component\Listing\Columns\Processed;

class ProcessedTest extends TestCase
{
    /**
     * @var MockObject
     */
    protected $timezone;

    /**
     * @var Processed
     */
    protected $column;

    public function setUp()
    {
        $context = $this->getMockBuilder(ContextInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $uiComponentFactory = $this->getMockBuilder(UiComponentFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->timezone = $this->getMockBuilder(TimezoneInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $booleanUtils = $this->getMockBuilder(BooleanUtils::class)
            ->disableOriginalConstructor()
            ->getMock();

        $components = [];
        $data = [];

        $resolver = $this->getMockBuilder(ResolverInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dataBundle = $this->getMockBuilder(DataBundle::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->column = new Processed(
            $context,
            $uiComponentFactory,
            $this->timezone,
            $booleanUtils,
            $components,
            $data,
            $resolver,
            $dataBundle
        );
    }

    /**
     * @param $columnName
     * @param $dateString
     * @param $gmtDateString
     * @param $dataSource
     * @param $expectedResult
     * @dataProvider dataProviderPrepareDataSource
     */
    public function testPrepareDataSource(
        $columnName,
        $dateString,
        $gmtDateString,
        $dataSource,
        $expectedResult
    ) {
        $dateFormat = 'Y-m-d H:i:s';

        $this->column->setData('name', $columnName);

        $date = $this->getMockBuilder(DateTime::class)
            ->disableOriginalConstructor()
            ->getMock();

        $date->expects($this->any())
            ->method('format')
            ->with($dateFormat)
            ->willReturn($gmtDateString);

        $this->timezone->expects($this->any())
            ->method('date')
            ->with(strtotime($dateString))
            ->willReturn($date);

        $this->assertEquals(
            $expectedResult,
            $this->column->prepareDataSource($dataSource)
        );
    }

    /**
     * @return array
     */
    public function dataProviderPrepareDataSource()
    {
        $date = '2020-01-01 12:00:00';
        $gmtDate = '2020-01-01 15:00:00';

        return [
            [
                'processed_at',
                $date,
                $gmtDate,
                [],
                []
            ],
            [
                'processed_at',
                $date,
                $gmtDate,
                ['data' => ['items' => [['entity' => 'Order']]]],
                ['data' => ['items' => [['entity' => 'Order', 'processed_at' => __('Pending')]]]]
            ],
            [
                'processed_at',
                $date,
                $gmtDate,
                ['data' => ['items' => [['entity' => 'Order', 'processed_at' => $date]]]],
                ['data' => ['items' => [['entity' => 'Order', 'processed_at' => $gmtDate]]]]
            ]
        ];
    }
}
