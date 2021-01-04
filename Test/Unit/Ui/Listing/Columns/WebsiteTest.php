<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Ui\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\WebsiteFactory;
use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Ui\Component\Listing\Columns\Website;

class WebsiteTest extends TestCase
{
    protected $column;

    protected $websiteFactory;

    public function setUp()
    {
        $context = $this->getMockBuilder(ContextInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $uiComponentFactory = $this->getMockBuilder(UiComponentFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->websiteFactory = $this->getMockBuilder(WebsiteFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $components = [];
        $data = [];

        $this->column = new Website(
            $context,
            $uiComponentFactory,
            $this->websiteFactory,
            $components,
            $data
        );
    }

    /**
     * @param $columnName
     * @param $scopeId
     * @param $websiteName
     * @param $dataSource
     * @param $expectedResult
     * @dataProvider dataProviderPrepareDataSource
     */
    public function testPrepareDataSource(
        $columnName,
        $scopeId,
        $websiteName,
        $dataSource,
        $expectedResult
    )
    {
        $this->column->setData('name', $columnName);

        $website = $this->getMockBuilder(\Magento\Store\Model\Website::class)
            ->disableOriginalConstructor()
            ->getMock();

        $website->expects($this->any())
            ->method('load')
            ->with($scopeId)
            ->willReturn($website);

        $website->expects($this->any())
            ->method('getName')
            ->willReturn($websiteName);

        $this->websiteFactory->expects($this->any())
            ->method('create')
            ->willReturn($website);

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
                'scope_id',
                1,
                'Test Website Name',
                [],
                []
            ],
            [
                'scope_id',
                1,
                'Test Website Name',
                ['data' => ['items' => [['entity' => 'Order']]]],
                ['data' => ['items' => [['entity' => 'Order', 'scope_id' => 'OrderFlow']]]]
            ],
            [
                'scope_id',
                1,
                'Test Website Name',
                ['data' => ['items' => [['entity' => 'Order', 'scope_id' => 1]]]],
                ['data' => ['items' => [['entity' => 'Order', 'scope_id' => 'Test Website Name']]]]
            ]
        ];
    }
}
