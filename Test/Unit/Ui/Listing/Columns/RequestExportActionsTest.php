<?php


namespace RealtimeDespatch\OrderFlow\Test\Unit\Ui\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Framework\UrlInterface;
use Magento\Framework\AuthorizationInterface;
use RealtimeDespatch\OrderFlow\Api\Data\ExportInterface;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Export as ResourceModel;
use RealtimeDespatch\OrderFlow\Ui\Component\Listing\Columns\RequestExportActions;

class RequestExportActionsTest extends TestCase
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
     * @var MockObject
     */
    protected $resourceModel;

    /**
     * @var RequestExportActions
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

        $this->resourceModel = $this->getMockBuilder(ResourceModel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $components = [];
        $data = [];

        $this->column = new RequestExportActions(
            $context,
            $uiComponentFactory,
            $this->url,
            $this->auth,
            $this->resourceModel,
            $components,
            $data
        );
    }

    /**
     * @param $entityType
     * @param $resourcePath
     * @param $isAllowed
     * @param $expectedResult
     * @@dataProvider dataProviderCanViewExport
     */
    public function testCanViewRequest(
        $entityType,
        $resourcePath,
        $isAllowed,
        $expectedResult
    )
    {
        $this->auth->expects($this->once())
            ->method('isAllowed')
            ->with($this->equalTo($resourcePath))
            ->willReturn($isAllowed);

        $this->assertEquals($expectedResult, $this->column->canViewExport($entityType));
    }

    /**
     * @return array
     */
    public function dataProviderCanViewExport()
    {
        return [
            [
                ExportInterface::ENTITY_PRODUCT,
                'RealtimeDespatch_OrderFlow::orderflow_exports_products',
                false,
                false,
            ],
            [
                ExportInterface::ENTITY_PRODUCT,
                'RealtimeDespatch_OrderFlow::orderflow_exports_products',
                true,
                true,
            ],
            [
                ExportInterface::ENTITY_ORDER,
                'RealtimeDespatch_OrderFlow::orderflow_exports_orders',
                false,
                false,
            ],
            [
                ExportInterface::ENTITY_ORDER,
                'RealtimeDespatch_OrderFlow::orderflow_exports_orders',
                true,
                true,
            ]
        ];
    }
}
