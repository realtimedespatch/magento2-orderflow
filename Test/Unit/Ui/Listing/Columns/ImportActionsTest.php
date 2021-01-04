<?php


namespace RealtimeDespatch\OrderFlow\Test\Unit\Ui\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Framework\UrlInterface;
use Magento\Framework\AuthorizationInterface;
use RealtimeDespatch\OrderFlow\Ui\Component\Listing\Columns\ImportActions;

class ImportActionsTest extends TestCase
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
     * @var ImportActions
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

        $this->column = new ImportActions(
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
            ->with($this->equalTo('RealtimeDespatch_OrderFlow::orderflow_requests_imports'))
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
     * @param $importId
     * @param $requestId
     * @param $importUrl
     * @param $requestUrl
     * @param $canViewRequest
     * @param $dataSource
     * @param $expectedResult
     * @dataProvider dataProviderPrepareDataSource
     */
    public function testPrepareDataSource(
        $columnName,
        $importId,
        $requestId,
        $importUrl,
        $requestUrl,
        $canViewRequest,
        $dataSource,
        $expectedResult
    )
    {
        $this->column->setData('name', $columnName);

        $this->auth->expects($this->once())
            ->method('isAllowed')
            ->with($this->equalTo('RealtimeDespatch_OrderFlow::orderflow_requests_imports'))
            ->willReturn($canViewRequest);

        $this->url->expects($this->any())
            ->method('getUrl')
            ->withConsecutive(
                [ImportActions::IMPORT_URL_PATH_VIEW, ['import_id' => $importId]],
                [ImportActions::REQUEST_URL_PATH_VIEW, ['request_id' => $requestId]]
            )
            ->will($this->onConsecutiveCalls($importUrl, $requestUrl));

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
        $importId = 2;
        $requestId = 1;
        $importUrl = 'https://www.example.com/import/id/1';
        $requestUrl = 'https://www.example.com/request/id/1';

        return [
            [
                'actions',
                $importId,
                $requestId,
                $importUrl,
                $requestUrl,
                false,
                ['data' => ['items' => [['import_id' => $importId]]]],
                [
                    'data' => [
                        'items' => [
                            [
                                'import_id' => $importId,
                                'actions' => [
                                    'view_import' => [
                                        'href' => $importUrl,
                                        'label' => __('View Import')
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                'actions',
                $importId,
                $requestId,
                $importUrl,
                $requestUrl,
                true,
                ['data' => ['items' => [['import_id' => $importId]]]],
                [
                    'data' => [
                        'items' => [
                            [
                                'import_id' => $importId,
                                'actions' => [
                                    'view_import' => [
                                        'href' => $importUrl,
                                        'label' => __('View Import')
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                'actions',
                $importId,
                $requestId,
                $importUrl,
                $requestUrl,
                true,
                ['data' => ['items' => [['import_id' => $importId, 'request_id' => $requestId]]]],
                [
                    'data' => [
                        'items' => [
                            [
                                'import_id' => $importId,
                                'request_id' => $requestId,
                                'actions' => [
                                    'view_import' => [
                                        'href' => $importUrl,
                                        'label' => __('View Import')
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
