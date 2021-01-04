<?php


namespace RealtimeDespatch\OrderFlow\Test\Unit\Ui\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Framework\UrlInterface;
use Magento\Framework\AuthorizationInterface;
use RealtimeDespatch\OrderFlow\Api\Data\ImportInterface;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Import as ResourceModel;
use RealtimeDespatch\OrderFlow\Ui\Component\Listing\Columns\RequestImportActions;

class RequestImportActionsTest extends TestCase
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
     * @var RequestImportActions
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

        $this->column = new RequestImportActions(
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
     * @@dataProvider dataProviderCanViewImport
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

        $this->assertEquals($expectedResult, $this->column->canViewImport($entityType));
    }

    /**
     * @return array
     */
    public function dataProviderCanViewImport()
    {
        return [
            [
                ImportInterface::ENTITY_INVENTORY,
                'RealtimeDespatch_OrderFlow::orderflow_imports_inventory',
                false,
                false,
            ],
            [
                ImportInterface::ENTITY_INVENTORY,
                'RealtimeDespatch_OrderFlow::orderflow_imports_inventory',
                true,
                true,
            ],
            [
                ImportInterface::ENTITY_SHIPMENT,
                'RealtimeDespatch_OrderFlow::orderflow_imports_shipments',
                false,
                false,
            ],
            [
                ImportInterface::ENTITY_SHIPMENT,
                'RealtimeDespatch_OrderFlow::orderflow_imports_shipments',
                true,
                true,
            ]
        ];
    }
}
