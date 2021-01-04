<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Ui\Listing\Container;

use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface as RtdRequest;
use RealtimeDespatch\OrderFlow\Api\RequestRepositoryInterface;
use RealtimeDespatch\OrderFlow\Ui\Component\Container\RequestLineListing;

class RequestLineListingTest extends TestCase
{
    /**
     * @var MockObject
     */
    protected $context;

    /**
     * @var MockObject
     */
    protected $request;

    /**
     * @var MockObject
     */
    protected $requestRepository;

    public function setUp()
    {
        $this->context = $this->getMockBuilder(ContextInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->request = $this->getMockBuilder(RequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestRepository = $this->getMockBuilder(RequestRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @dataProvider dataProviderConfigureRenderUrls
     * @param int $requestId
     * @param boolean $isExport
     * @param array $data
     * @param array $expectedResult
     */
    public function testConstruct(
        int $requestId,
        bool $isExport,
        array $data,
        array $expectedResult
    ) {
        $this->request->expects($this->any())
            ->method('getParam')
            ->with($this->equalTo('request_id'))
            ->willReturn($requestId);

        $request = $this->getMockBuilder(RtdRequest::class)
            ->disableOriginalConstructor()
            ->getMock();

        $request->expects($this->any())
            ->method('isExport')
            ->willReturn($isExport);

        $this->requestRepository->expects($this->once())
            ->method('get')
            ->with($this->equalTo($requestId))
            ->willReturn($request);

        $components = [];

        $container = new RequestLineListing(
            $this->context,
            $this->request,
            $this->requestRepository,
            $components,
            $data
        );

        $this->assertEquals($expectedResult, $container->getData());
    }

    public function dataProviderConfigureRenderUrls()
    {
        $requestId = 1;

        return [
            [
                $requestId,
                true,
                [
                    'config' => [
                        'render_url' => 'http://www.example.com/mui/index/render/',
                        'update_url' => 'http://www.example.com/mui/index/render/',
                        'ns' => 'request_import_line_listing',
                        'dataScope' => 'request_import_line_listing'
                    ]
                ],
                [
                    'config' => [
                        'render_url' => 'http://www.example.com/mui/index/render/request_id/'.$requestId,
                        'update_url' => 'http://www.example.com/mui/index/render/request_id/'.$requestId,
                        'ns' => 'request_export_line_listing',
                        'dataScope' => 'request_export_line_listing'
                    ]
                ]
            ],
            [
                $requestId,
                false,
                [
                    'config' => [
                        'render_url' => 'http://www.example.com/mui/index/render/',
                        'update_url' => 'http://www.example.com/mui/index/render/',
                        'ns' => 'request_import_line_listing',
                        'dataScope' => 'request_import_line_listing'
                    ]
                ],
                [
                    'config' => [
                        'render_url' => 'http://www.example.com/mui/index/render/request_id/'.$requestId,
                        'update_url' => 'http://www.example.com/mui/index/render/request_id/'.$requestId,
                        'ns' => 'request_import_line_listing',
                        'dataScope' => 'request_import_line_listing'
                    ]
                ]
            ]
        ];
    }

    /**
     * @dataProvider dataProviderConstructWithException
     * @param int $requestId
     * @param array $data
     * @param array $expectedResult
     */
    public function testConstructWithException(
        int $requestId,
        array $data,
        array $expectedResult
    ) {
        $this->request->expects($this->any())
            ->method('getParam')
            ->with($this->equalTo('request_id'))
            ->willReturn($requestId);

        $this->requestRepository->expects($this->once())
            ->method('get')
            ->with($this->equalTo($requestId))
            ->willThrowException(new Exception);

        $components = [];

        $container = new RequestLineListing(
            $this->context,
            $this->request,
            $this->requestRepository,
            $components,
            $data
        );

        $this->assertEquals($expectedResult, $container->getData());
    }

    public function dataProviderConstructWithException()
    {
        $requestId = 1;

        return [
            [
                $requestId,
                [
                    'config' => [
                        'render_url' => 'http://www.example.com/mui/index/render/',
                        'update_url' => 'http://www.example.com/mui/index/render/',
                        'ns' => 'request_import_line_listing',
                        'dataScope' => 'request_import_line_listing'
                    ]
                ],
                [
                    'config' => [
                        'render_url' => 'http://www.example.com/mui/index/render/request_id/'.$requestId,
                        'update_url' => 'http://www.example.com/mui/index/render/request_id/'.$requestId,
                        'ns' => 'request_import_line_listing',
                        'dataScope' => 'request_import_line_listing'
                    ]
                ]
            ],
            [
                $requestId,
                [
                    'config' => [
                        'render_url' => 'http://www.example.com/mui/index/render/',
                        'update_url' => 'http://www.example.com/mui/index/render/',
                        'ns' => 'request_export_line_listing',
                        'dataScope' => 'request_export_line_listing'
                    ]
                ],
                [
                    'config' => [
                        'render_url' => 'http://www.example.com/mui/index/render/request_id/'.$requestId,
                        'update_url' => 'http://www.example.com/mui/index/render/request_id/'.$requestId,
                        'ns' => 'request_export_line_listing',
                        'dataScope' => 'request_export_line_listing'
                    ]
                ]
            ]
        ];
    }
}
