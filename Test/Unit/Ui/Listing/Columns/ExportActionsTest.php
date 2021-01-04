<?php


namespace RealtimeDespatch\OrderFlow\Test\Unit\Ui\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Framework\UrlInterface;
use Magento\Framework\AuthorizationInterface;
use RealtimeDespatch\OrderFlow\Ui\Component\Listing\Columns\ExportActions;

class ExportActionsTest extends TestCase
{
    /**
     * @var MockObject
     */
    protected $url;

    /**
     * @var MockObject
     */
    protected $auth;

    /**
     * @var ExportActions
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

        $this->url = $this->getMockBuilder(UrlInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->auth = $this->getMockBuilder(AuthorizationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $components = [];
        $data = [];

        $this->column = new ExportActions(
            $context,
            $uiComponentFactory,
            $this->url,
            $this->auth,
            $components,
            $data
        );
    }

    /**
     * @param $authResponse
     * @param $expectedResult
     * @@dataProvider dataProviderCanViewRequest
     */
    public function testCanViewRequest($authResponse, $expectedResult)
    {
        $this->auth->expects($this->once())
            ->method('isAllowed')
            ->with($this->equalTo('RealtimeDespatch_OrderFlow::orderflow_requests_exports'))
            ->willReturn($authResponse);

        $this->assertEquals($expectedResult, $this->column->canViewRequest());
    }

    /**
     * @return array
     */
    public function dataProviderCanViewRequest()
    {
        return [[false, false], [true, true]];
    }

    /**
     * @param $columnName
     * @param $exportId
     * @param $requestId
     * @param $exportUrl
     * @param $requestUrl
     * @param $canViewRequest
     * @param $dataSource
     * @param $expectedResult
     * @dataProvider dataProviderPrepareDataSource
     */
    public function testPrepareDataSource(
        $columnName,
        $exportId,
        $requestId,
        $exportUrl,
        $requestUrl,
        $canViewRequest,
        $dataSource,
        $expectedResult
    )
    {
        $this->column->setData('name', $columnName);

        $this->auth->expects($this->once())
            ->method('isAllowed')
            ->with($this->equalTo('RealtimeDespatch_OrderFlow::orderflow_requests_exports'))
            ->willReturn($canViewRequest);

        $this->url->expects($this->any())
            ->method('getUrl')
            ->withConsecutive(
                [ExportActions::IMPORT_URL_PATH_VIEW, ['export_id' => $exportId]],
                [ExportActions::REQUEST_URL_PATH_VIEW, ['request_id' => $requestId]]
            )
            ->will($this->onConsecutiveCalls($exportUrl, $requestUrl));

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
        $exportId = 2;
        $requestId = 1;
        $exportUrl = 'https://www.example.com/export/id/1';
        $requestUrl = 'https://www.example.com/request/id/1';

        return [
            [
                'actions',
                $exportId,
                $requestId,
                $exportUrl,
                $requestUrl,
                false,
                ['data' => ['items' => [['export_id' => $exportId]]]],
                [
                    'data' => [
                        'items' => [
                            [
                                'export_id' => $exportId,
                                'actions' => [
                                    'view_export' => [
                                        'href' => $exportUrl,
                                        'label' => __('View Export')
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                'actions',
                $exportId,
                $requestId,
                $exportUrl,
                $requestUrl,
                true,
                ['data' => ['items' => [['export_id' => $exportId]]]],
                [
                    'data' => [
                        'items' => [
                            [
                                'export_id' => $exportId,
                                'actions' => [
                                    'view_export' => [
                                        'href' => $exportUrl,
                                        'label' => __('View Export')
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                'actions',
                $exportId,
                $requestId,
                $exportUrl,
                $requestUrl,
                true,
                ['data' => ['items' => [['export_id' => $exportId, 'request_id' => $requestId]]]],
                [
                    'data' => [
                        'items' => [
                            [
                                'export_id' => $exportId,
                                'request_id' => $requestId,
                                'actions' => [
                                    'view_export' => [
                                        'href' => $exportUrl,
                                        'label' => __('View Export')
                                    ],
                                    'view_request' => [
                                        'href' => $requestUrl,
                                        'label' => __('View Processed Request')
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];
    }
}
