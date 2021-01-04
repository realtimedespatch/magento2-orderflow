<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Ui\Listing\Columns;

use PHPUnit\Framework\TestCase;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use RealtimeDespatch\OrderFlow\Ui\Component\Listing\Columns\Operation;
use RealtimeDespatch\OrderFlow\Model\Source\OperationSource;

class OperationTest extends TestCase
{
    /**
     * @var Operation
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

        $this->column = new Operation(
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
                'operation',
                [],
                []
            ],
            [
                'operation',
                ['data' => ['items' => [['entity' => 'Order', 'operation' => __(OperationSource::OPERATION_CREATE)]]]],
                ['data' => ['items' => [['entity' => 'Order', 'operation' => __(OperationSource::OPERATION_QUEUE)]]]]
            ],
            [
                'operation',
                ['data' => ['items' => [['entity' => 'Order', 'operation' => __(OperationSource::OPERATION_UPDATE)]]]],
                ['data' => ['items' => [['entity' => 'Order', 'operation' => __(OperationSource::OPERATION_QUEUE)]]]]
            ],
            [
                'operation',
                ['data' => ['items' => [['entity' => 'Order', 'operation' => __(OperationSource::OPERATION_EXPORT)]]]],
                ['data' => ['items' => [['entity' => 'Order', 'operation' => __(OperationSource::OPERATION_EXPORT)]]]]
            ]
        ];
    }
}
