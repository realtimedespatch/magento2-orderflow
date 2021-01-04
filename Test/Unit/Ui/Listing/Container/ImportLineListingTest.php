<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Ui\Listing\Container;

use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use RealtimeDespatch\OrderFlow\Api\Data\ImportInterface;
use RealtimeDespatch\OrderFlow\Api\ImportRepositoryInterface;
use RealtimeDespatch\OrderFlow\Ui\Component\Container\ImportLineListing;

class ImportLineListingTest extends TestCase
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
    protected $importRepository;

    public function setUp()
    {
        $this->context = $this->getMockBuilder(ContextInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->request = $this->getMockBuilder(RequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->importRepository = $this->getMockBuilder(ImportRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @dataProvider dataProviderConfigureRenderUrls
     * @param int $importId
     * @param boolean $isShipmentImport
     * @param array $data
     * @param array $expectedResult
     */
    public function testConstruct(
        int $importId,
        bool $isShipmentImport,
        array $data,
        array $expectedResult
    ) {
        $this->request->expects($this->any())
            ->method('getParam')
            ->with($this->equalTo('import_id'))
            ->willReturn($importId);

        $import = $this->getMockBuilder(ImportInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $import->expects($this->any())
            ->method('isShipmentImport')
            ->willReturn($isShipmentImport);

        $this->importRepository->expects($this->once())
            ->method('get')
            ->with($this->equalTo($importId))
            ->willReturn($import);

        $components = [];

        $container = new ImportLineListing(
            $this->context,
            $this->request,
            $this->importRepository,
            $components,
            $data
        );

        $this->assertEquals($expectedResult, $container->getData());
    }

    public function dataProviderConfigureRenderUrls()
    {
        $importId = 1;

        return [
            [
                $importId,
                true,
                [
                    'config' => [
                        'render_url' => 'http://www.example.com/mui/index/render/',
                        'update_url' => 'http://www.example.com/mui/index/render/',
                        'ns' => 'inventory_import_line_listing',
                        'dataScope' => 'inventory_import_line_listing'
                    ]
                ],
                [
                    'config' => [
                        'render_url' => 'http://www.example.com/mui/index/render/import_id/'.$importId,
                        'update_url' => 'http://www.example.com/mui/index/render/import_id/'.$importId,
                        'ns' => 'shipment_import_line_listing',
                        'dataScope' => 'shipment_import_line_listing'
                    ]
                ]
            ],
            [
                $importId,
                false,
                [
                    'config' => [
                        'render_url' => 'http://www.example.com/mui/index/render/',
                        'update_url' => 'http://www.example.com/mui/index/render/',
                        'ns' => 'inventory_import_line_listing',
                        'dataScope' => 'inventory_import_line_listing'
                    ]
                ],
                [
                    'config' => [
                        'render_url' => 'http://www.example.com/mui/index/render/import_id/'.$importId,
                        'update_url' => 'http://www.example.com/mui/index/render/import_id/'.$importId,
                        'ns' => 'inventory_import_line_listing',
                        'dataScope' => 'inventory_import_line_listing'
                    ]
                ]
            ]
        ];
    }

    /**
     * @dataProvider dataProviderConstructWithException
     * @param int $importId
     * @param array $data
     * @param array $expectedResult
     */
    public function testConstructWithException(
        int $importId,
        array $data,
        array $expectedResult
    ) {
        $this->request->expects($this->any())
            ->method('getParam')
            ->with($this->equalTo('import_id'))
            ->willReturn($importId);

        $this->importRepository->expects($this->once())
            ->method('get')
            ->with($this->equalTo($importId))
            ->willThrowException(new Exception);

        $components = [];

        $container = new ImportLineListing(
            $this->context,
            $this->request,
            $this->importRepository,
            $components,
            $data
        );

        $this->assertEquals($expectedResult, $container->getData());
    }

    public function dataProviderConstructWithException()
    {
        $importId = 1;

        return [
            [
                $importId,
                [
                    'config' => [
                        'render_url' => 'http://www.example.com/mui/index/render/',
                        'update_url' => 'http://www.example.com/mui/index/render/',
                        'ns' => 'inventory_import_line_listing',
                        'dataScope' => 'inventory_import_line_listing'
                    ]
                ],
                [
                    'config' => [
                        'render_url' => 'http://www.example.com/mui/index/render/import_id/'.$importId,
                        'update_url' => 'http://www.example.com/mui/index/render/import_id/'.$importId,
                        'ns' => 'inventory_import_line_listing',
                        'dataScope' => 'inventory_import_line_listing'
                    ]
                ]
            ],
            [
                $importId,
                [
                    'config' => [
                        'render_url' => 'http://www.example.com/mui/index/render/',
                        'update_url' => 'http://www.example.com/mui/index/render/',
                        'ns' => 'shipment_import_line_listing',
                        'dataScope' => 'shipment_import_line_listing'
                    ]
                ],
                [
                    'config' => [
                        'render_url' => 'http://www.example.com/mui/index/render/import_id/'.$importId,
                        'update_url' => 'http://www.example.com/mui/index/render/import_id/'.$importId,
                        'ns' => 'shipment_import_line_listing',
                        'dataScope' => 'shipment_import_line_listing'
                    ]
                ]
            ]
        ];
    }
}
