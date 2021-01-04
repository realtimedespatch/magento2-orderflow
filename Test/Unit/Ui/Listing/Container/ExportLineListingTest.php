<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Ui\Listing\Container;

use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use RealtimeDespatch\OrderFlow\Api\Data\ExportInterface;
use RealtimeDespatch\OrderFlow\Api\ExportRepositoryInterface;
use RealtimeDespatch\OrderFlow\Ui\Component\Container\ExportLineListing;

class ExportLineListingTest extends TestCase
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
    protected $exportRepository;

    public function setUp()
    {
        $this->context = $this->getMockBuilder(ContextInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->request = $this->getMockBuilder(RequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->exportRepository = $this->getMockBuilder(ExportRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @dataProvider dataProviderConfigureRenderUrls
     * @param int $exportId
     * @param boolean $isOrderExport
     * @param array $data
     * @param array $expectedResult
     */
    public function testConstruct(
        int $exportId,
        bool $isOrderExport,
        array $data,
        array $expectedResult
    ) {
        $this->request->expects($this->any())
            ->method('getParam')
            ->with($this->equalTo('export_id'))
            ->willReturn($exportId);

        $export = $this->getMockBuilder(ExportInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $export->expects($this->any())
            ->method('isOrderExport')
            ->willReturn($isOrderExport);

        $this->exportRepository->expects($this->once())
            ->method('get')
            ->with($this->equalTo($exportId))
            ->willReturn($export);

        $components = [];

        $container = new ExportLineListing(
            $this->context,
            $this->request,
            $this->exportRepository,
            $components,
            $data
        );

        $this->assertEquals($expectedResult, $container->getData());
    }

    public function dataProviderConfigureRenderUrls()
    {
        $exportId = 1;

        return [
            [
                $exportId,
                true,
                [
                    'config' => [
                        'render_url' => 'http://www.example.com/mui/index/render/',
                        'update_url' => 'http://www.example.com/mui/index/render/',
                        'ns' => 'product_export_line_listing',
                        'dataScope' => 'product_export_line_listing'
                    ]
                ],
                [
                    'config' => [
                        'render_url' => 'http://www.example.com/mui/index/render/export_id/'.$exportId,
                        'update_url' => 'http://www.example.com/mui/index/render/export_id/'.$exportId,
                        'ns' => 'order_export_line_listing',
                        'dataScope' => 'order_export_line_listing'
                    ]
                ]
            ],
            [
                $exportId,
                false,
                [
                    'config' => [
                        'render_url' => 'http://www.example.com/mui/index/render/',
                        'update_url' => 'http://www.example.com/mui/index/render/',
                        'ns' => 'product_export_line_listing',
                        'dataScope' => 'product_export_line_listing'
                    ]
                ],
                [
                    'config' => [
                        'render_url' => 'http://www.example.com/mui/index/render/export_id/'.$exportId,
                        'update_url' => 'http://www.example.com/mui/index/render/export_id/'.$exportId,
                        'ns' => 'product_export_line_listing',
                        'dataScope' => 'product_export_line_listing'
                    ]
                ]
            ]
        ];
    }

    /**
     * @dataProvider dataProviderConstructWithException
     * @param int $exportId
     * @param array $data
     * @param array $expectedResult
     */
    public function testConstructWithException(
        int $exportId,
        array $data,
        array $expectedResult
    ) {
        $this->request->expects($this->any())
            ->method('getParam')
            ->with($this->equalTo('export_id'))
            ->willReturn($exportId);

        $this->exportRepository->expects($this->once())
            ->method('get')
            ->with($this->equalTo($exportId))
            ->willThrowException(new Exception);

        $components = [];

        $container = new ExportLineListing(
            $this->context,
            $this->request,
            $this->exportRepository,
            $components,
            $data
        );

        $this->assertEquals($expectedResult, $container->getData());
    }

    public function dataProviderConstructWithException()
    {
        $exportId = 1;

        return [
            [
                $exportId,
                [
                    'config' => [
                        'render_url' => 'http://www.example.com/mui/index/render/',
                        'update_url' => 'http://www.example.com/mui/index/render/',
                        'ns' => 'order_export_line_listing',
                        'dataScope' => 'order_export_line_listing'
                    ]
                ],
                [
                    'config' => [
                        'render_url' => 'http://www.example.com/mui/index/render/export_id/'.$exportId,
                        'update_url' => 'http://www.example.com/mui/index/render/export_id/'.$exportId,
                        'ns' => 'order_export_line_listing',
                        'dataScope' => 'order_export_line_listing'
                    ]
                ]
            ],
            [
                $exportId,
                [
                    'config' => [
                        'render_url' => 'http://www.example.com/mui/index/render/',
                        'update_url' => 'http://www.example.com/mui/index/render/',
                        'ns' => 'product_export_line_listing',
                        'dataScope' => 'product_export_line_listing'
                    ]
                ],
                [
                    'config' => [
                        'render_url' => 'http://www.example.com/mui/index/render/export_id/'.$exportId,
                        'update_url' => 'http://www.example.com/mui/index/render/export_id/'.$exportId,
                        'ns' => 'product_export_line_listing',
                        'dataScope' => 'product_export_line_listing'
                    ]
                ]
            ]
        ];
    }
}
