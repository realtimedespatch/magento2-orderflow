<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Ui\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Ui\Component\Listing\Columns\UnitsCalculated;

class UnitsCalculatedTest extends TestCase
{
    /**
     * @var UnitsCalculated
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

        $components = [];
        $data = [];

        $this->column = new UnitsCalculated(
            $context,
            $uiComponentFactory,
            $components,
            $data
        );
    }

    /**
     * @param $columnName
     * @param $dataSource
     * @param $expectedResult
     * @dataProvider dataProviderPrepareDataSource
     */
    public function testPrepareDataSource($columnName, $dataSource, $expectedResult)
    {
        $this->column->setData('name', $columnName);

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
        return [
            [
                'units_received',
                [],
                []
            ],
            [
                'units_calculated',
                ['data' => ['items' => [['reference' => '12345']]]],
                ['data' => ['items' => [['reference' => '12345']]]],
            ],
            [
                'units_calculated',
                ['data' => ['items' => [['reference' => '12345', 'additional_data' => false]]]],
                ['data' => ['items' => [['reference' => '12345', 'additional_data' => false, 'units_calculated' => 0]]]],
            ],
            [
                'units_calculated',
                ['data' => [
                    'items' =>
                        [
                            [
                                'reference' => '12345',
                                'additional_data' => '{"unitsCalculated":10,"unitsUnsentOrders":5,"unitsActiveQuotes":2,"unitsCalculated":3}',
                            ]
                        ]
                ]],
                ['data' => [
                    'items' =>
                        [
                            [
                                'reference' => '12345',
                                'additional_data' => '{"unitsCalculated":10,"unitsUnsentOrders":5,"unitsActiveQuotes":2,"unitsCalculated":3}',
                                'units_calculated' => 3
                            ]
                        ]
                ]]
            ]
        ];
    }
}
