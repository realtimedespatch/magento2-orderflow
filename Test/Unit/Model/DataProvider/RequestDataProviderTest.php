<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\DataProvider;

use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use RealtimeDespatch\OrderFlow\Model\DataProvider\RequestDataProvider;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\SearchResultInterface;
use RealtimeDespatch\OrderFlow\Model\Request;

class RequestDataProviderTest extends \PHPUnit\Framework\TestCase
{
    protected RequestDataProvider $exportDataProvider;
    protected ReportingInterface $mockReporting;
    protected SearchCriteriaBuilder $mockSearchCriteriaBuilder;
    protected RequestInterface $mockRequest;
    protected FilterBuilder $mockFilterBuilder;
    protected Filter $mockFilter;
    protected string $name;

    protected function setUp(): void
    {
        $this->mockReporting = $this->createMock(ReportingInterface::class);
        $this->mockSearchCriteriaBuilder = $this->createMock(SearchCriteriaBuilder::class);
        $this->mockRequest = $this->createMock(RequestInterface::class);
        $this->mockFilterBuilder = $this->createMock(FilterBuilder::class);
        $this->mockFilter = $this->createMock(Filter::class);

        $this->mockFilterBuilder
            ->expects($this->once())
            ->method('setField')
            ->with('entity')
            ->willReturnSelf();

        $this->mockFilterBuilder
            ->expects($this->once())
            ->method('setValue')
            ->with('product')
            ->willReturnSelf();

        $this->mockFilterBuilder
            ->expects($this->once())
            ->method('setConditionType')
            ->with('eq')
            ->willReturnSelf();

        $this->mockFilterBuilder
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->mockFilter);

        $this->mockSearchCriteriaBuilder
            ->expects($this->once())
            ->method('addFilter')
            ->with($this->mockFilter)
            ->willReturnSelf();

        $this->name = 'product_export_listing_data_source';

        $this->exportDataProvider = new RequestDataProvider(
            $this->name,
            'export_id',
            'id',
            $this->mockReporting,
            $this->mockSearchCriteriaBuilder,
            $this->mockRequest,
            $this->mockFilterBuilder,
            [],
            [
                'config' => [
                    'component' => 'Magento_Ui/js/grid/provider',
                    'update_url' => '/entity/product/',
                    'storageConfig' => [
                        'indexField' => 'export_id',
                    ],
                    'filter_url_params' => [
                        'entity' => 'product'
                    ]
                ]
            ]
        );
    }

    public function testGetData(): void
    {
        $mockSearchCriteria = $this->createMock(SearchCriteriaInterface::class);
        $mockSearchResult = $this->createMock(SearchResultInterface::class);

        $this->mockSearchCriteriaBuilder
            ->expects($this->once())
            ->method('create')
            ->willReturn($mockSearchCriteria);

        $mockSearchCriteria
            ->expects($this->once())
            ->method('setRequestName')
            ->with($this->name);

        $this->mockReporting
            ->expects($this->once())
            ->method('search')
            ->with($mockSearchCriteria)
            ->willReturn($mockSearchResult);

        $mockItems = $this->getMockSearchResultItems();
        $mockSearchResult
            ->expects($this->once())
            ->method('getItems')
            ->willReturn($mockItems);

        $mockSearchResult
            ->expects($this->once())
            ->method('getTotalCount')
            ->willReturn(sizeof($mockItems));

        $result = $this->exportDataProvider->getData();
        $this->assertIsArray($result);

        $expectedItems = [];
        foreach ($mockItems as $i => $mockItem) {
            $expectedItems[] = [
                'id' => $i,
            ];
        }

        $expectedResult = [
            'totalRecords' => sizeof($mockItems),
            'items' => $expectedItems,
        ];

        $this->assertEquals($expectedResult, $result);
    }

    protected function getMockSearchResultItems(): array
    {
        $n = rand(1, 10);
        $items = [];

        for ($i = 0; $i < $n; $i++) {

            $mockIdAttribute = $this->createMock(\Magento\Framework\Api\AttributeInterface::class);
            $mockIdAttribute
                ->expects($this->once())
                ->method('getAttributeCode')
                ->willReturn('id');
            $mockIdAttribute
                ->expects($this->once())
                ->method('getValue')
                ->willReturn($i);

            $mockRequest = $this->getMockBuilder(Request::class)
                ->disableOriginalConstructor()
                ->addMethods(['getCustomAttributes'])
                ->onlyMethods(['setResponseBody', 'setRequestBody'])
                ->getMock();

            $mockRequest
                ->expects($this->once())
                ->method('setResponseBody')
                ->with(null);

            $mockRequest
                ->expects($this->once())
                ->method('setRequestBody')
                ->with(null);

            $mockRequest
                ->expects($this->once())
                ->method('getCustomAttributes')
                ->willReturn([
                    $mockIdAttribute,
                ]);

            $items[] = $mockRequest;
        }

        return $items;
    }
}