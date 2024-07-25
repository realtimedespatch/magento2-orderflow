<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\Service;

use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\Data\ShipmentItemInterface;
use Magento\Sales\Model\Convert\Order as OrderConvert;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Magento\Shipping\Model\Order\TrackFactory;
use Magento\Shipping\Model\ShipmentNotifier;
use Psr\Log\LoggerInterface;
use RealtimeDespatch\OrderFlow\Helper\Import\Shipment as ShipmentHelper;
use RealtimeDespatch\OrderFlow\Model\Builder\RequestBuilder;
use RealtimeDespatch\OrderFlow\Model\Service\OrderRequestService;
use RealtimeDespatch\OrderFlow\Model\Service\ShipmentService;


class ShipmentServiceTest extends \PHPUnit\Framework\TestCase
{
    protected RequestBuilder $mockRequestBuilder;
    protected ShipmentService $shipmentService;
    protected LoggerInterface $mockLogger;
    protected ManagerInterface $mockEventManager;
    protected ShipmentHelper $mockShipmentHelper;
    protected OrderFactory $mockOrderFactory;
    protected TrackFactory $mockTrackFactory;
    protected ShipmentNotifier $mockShipmentNotifier;
    protected ModuleManager $mockModuleManager;
    protected OrderConvert $mockOrderConvert;
    protected object $params;
    protected Order $mockOrder;
    protected OrderItemInterface $mockOrderItem;
    protected ShipmentItemInterface $mockShipmentItem;
    protected Order\Shipment $mockShipment;
    protected Order\Shipment\Track $mockTrack;


    protected function setUp(): void
    {
        $incrementId = '10000001';

        $this->mockRequestBuilder = $this->createMock(RequestBuilder::class);
        $this->mockLogger = $this->createMock(LoggerInterface::class);
        $this->mockEventManager = $this->createMock(ManagerInterface::class);
        $this->mockShipmentHelper = $this->createMock(ShipmentHelper::class);
        $this->mockOrderFactory = $this->createMock(OrderFactory::class);
        $this->mockTrackFactory = $this->createMock(TrackFactory::class);
        $this->mockShipmentNotifier = $this->createMock(ShipmentNotifier::class);
        $this->mockModuleManager = $this->createMock(ModuleManager::class);
        $this->mockOrderConvert = $this->createMock(OrderConvert::class);
        $this->mockOrder = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->addMethods(['setIsInProcess'])
            ->onlyMethods(['loadByAttribute', 'canShip', 'getAllItems', 'save'])
            ->getMock();
        $this->mockOrderItem = $this->createMock(\Magento\Sales\Model\Order\Item::class);
        $this->mockShipmentItem = $this->createMock(\Magento\Sales\Model\Order\Shipment\Item::class);
        $this->mockShipment = $this->createMock(Order\Shipment::class);
        $this->mockTrack = $this->createMock(Order\Shipment\Track::class);

        $this->params = (object) [
            'orderIncrementId' => $incrementId,
            'comment' => 'Test Comment',
            'email' => true,
            'includeComment' => true,
            'skuQtys' => (object) [
                (object) [
                    'sku' => 'SKU123',
                    'qty' => 10,
                    'source' => 'default',
                ]
            ],
            'tracks' => [],
        ];

        $this->mockOrderFactory
            ->method('create')
            ->willReturn($this->mockOrder);

        $this->mockOrder
            ->method('loadByAttribute')
            ->with('increment_id', $incrementId)
            ->willReturnSelf();

        $this->shipmentService = new ShipmentService(
            $this->mockLogger,
            $this->mockEventManager,
            $this->mockShipmentHelper,
            $this->mockOrderFactory,
            $this->mockTrackFactory,
            $this->mockOrderConvert,
            $this->mockShipmentNotifier,
            $this->mockModuleManager
        );
    }

    public function testCreateShipments(): void
    {
        $this->mockOrder
            ->expects($this->once())
            ->method('canShip')
            ->willReturn(true);

        $this->mockOrderConvert
            ->expects($this->once())
            ->method('toShipment')
            ->with($this->mockOrder)
            ->willReturn($this->mockShipment);

        $this->mockShipment
            ->expects($this->once())
            ->method('addComment')
            ->with(
                'Test Comment',
                true
            )
            ->willReturnSelf();

        $this->mockOrderItem
            ->expects($this->once())
            ->method('getSku')
            ->willReturn('SKU123');

        $this->mockShipment
            ->expects($this->exactly(3))
            ->method('getOrder')
            ->willReturn($this->mockOrder);

        $this->mockOrder
            ->expects($this->once())
            ->method('getAllItems')
            ->willReturn([
                $this->mockOrderItem,
            ]);

        $this->mockOrderConvert
            ->expects($this->once())
            ->method('itemToShipmentItem')
            ->with($this->mockOrderItem)
            ->willReturn($this->mockShipmentItem);

        $this->mockShipmentItem
            ->expects($this->once())
            ->method('setQty')
            ->with(10)
            ->willReturnSelf();

        $this->params->trackingNumber = 'ABC12345';
        $this->params->courierName = 'TestCourier';
        $this->params->serviceName = 'TestService';

        $this->mockTrackFactory
            ->expects($this->atLeast(1))
            ->method('create')
            ->willReturn($this->mockTrack);

        $this->mockTrack
            ->expects($this->atLeast(1))
            ->method('setCarrierCode')
            ->with('custom')
            ->willReturnSelf();

        $this->mockTrack
            ->expects($this->atLeast(1))
            ->method('setTitle')
            ->with("{$this->params->courierName} {$this->params->serviceName}")
            ->willReturnSelf();

        $this->mockTrack
            ->expects($this->atLeast(1))
            ->method('setNumber')
            ->with($this->params->trackingNumber)
            ->willReturnSelf();

        $this->mockShipment
            ->expects($this->once())
            ->method('register');

        $this->mockShipment
            ->expects($this->exactly(2))
            ->method('save');

        $this->mockOrder
            ->expects($this->once())
            ->method('setIsInProcess')
            ->with(true)
            ->willReturnSelf();

        $this->mockOrder
            ->expects($this->atLeast(1))
            ->method('save');

        $this->mockShipmentNotifier
            ->expects($this->once())
            ->method('notify')
            ->with($this->mockShipment);

        $this->shipmentService->createShipments($this->params);
    }

    public function testCreateShipmentsMsi(): void
    {
        $this->mockModuleManager
            ->expects($this->once())
            ->method('isEnabled')
            ->with('Magento_InventoryShipping')
            ->willReturn(true);

        $this->mockShipment
            ->expects($this->once())
            ->method('getExtensionAttributes')
            ->willReturn(
                $this->getMockBuilder(\Magento\Sales\Api\Data\ShipmentExtensionInterface::class)
                    ->onlyMethods(['setSourceCode', 'getSourceCode'])
                    ->getMock()
            );

        $this->testCreateShipments();
    }

    public function testCreateShipmentsMultiTrack(): void
    {
        $this->params->tracks = [
            (object) [
                'trackingNumber' => 'ABC12345',
            ]
        ];

        $this->testCreateShipments();
    }

    public function testCreateShipmentsUnshippable(): void
    {
        $this->mockOrder->expects($this->once())->method('canShip')->willReturn(false);
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage("Can't create shipment");
        $this->mockOrderConvert->expects($this->never())->method('toShipment');
        $this->shipmentService->createShipments($this->params);
    }
}