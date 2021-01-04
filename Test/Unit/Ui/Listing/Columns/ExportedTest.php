<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Ui\Listing\Columns;

use PHPUnit\Framework\TestCase;
use Magento\Framework\Locale\Bundle\DataBundle;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Stdlib\BooleanUtils;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use RealtimeDespatch\OrderFlow\Ui\Component\Listing\Columns\Exported;

class ExportedTest extends TestCase
{
    /**
     * @var Exported
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

        $timeZone = $this->getMockBuilder(TimezoneInterface::class)
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

        $this->column = new Exported(
            $context,
            $uiComponentFactory,
            $timeZone,
            $booleanUtils,
            $components,
            $data,
            $resolver,
            $dataBundle
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
                'exported',
                [],
                []
            ],
            [
                'exported',
                ['data' => ['items' => [['sku' => '12345', 'exported' => __('Exported')]]]],
                ['data' => ['items' => [['sku' => '12345', 'exported' => __('Exported')]]]]
            ],
            [
                'exported',
                ['data' => ['items' => [['sku' => '12345']]]],
                ['data' => ['items' => [['sku' => '12345', 'exported' => __('Pending')]]]]
            ]
        ];
    }
}
