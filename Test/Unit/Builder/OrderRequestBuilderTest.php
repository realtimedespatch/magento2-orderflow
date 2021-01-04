<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Builder;

use Exception;
use Magento\Sales\Model\Order;
use Magento\Store\Model\Website;
use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use RealtimeDespatch\OrderFlow\Builder\OrderRequestBuilder;
use RealtimeDespatch\OrderFlow\Helper\Export\Order as OrderHelper;
use RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface;
use Magento\Framework\Serialize\Serializer\Json;

class OrderRequestBuilderTest extends TestCase
{
    protected $helper;
    protected $requestBuilder;
    protected $json;
    protected $website;
    protected $builder;

    public function setUp()
    {
        $this->helper = $this->getMockBuilder(OrderHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestBuilder = $this->getMockBuilder(RequestBuilderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->json = $this->getMockBuilder(Json::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->website = $this->getMockBuilder(Website::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->builder = new OrderRequestBuilder(
            $this->helper,
            $this->requestBuilder,
            $this->json
        );
    }

    public function testBuildWhenThereAreNoCreatableOrdersAvailable()
    {
        $orders = [];

        $this->website->expects($this->never())
            ->method('getId');

        $this->helper->expects($this->once())
            ->method('getCreateableOrders')
            ->willReturn($orders);

        $this->requestBuilder->expects($this->never())
            ->method('saveRequest');

        $this->builder->build($this->website);
    }

    public function testBuildWhenOrderHelperThrowsException()
    {
        $this->website->expects($this->never())
            ->method('getId');

        $this->helper->expects($this->once())
            ->method('getCreateableOrders')
            ->willThrowException(new Exception('Exception'));

        $this->requestBuilder->expects($this->never())
            ->method('saveRequest');

        $this->builder->build($this->website);
    }

    public function testBuildWhenThereAreCreateableOrdersAvailable()
    {
        $websiteId = 1234;

        // Order One
        $orderOneEntityId = 222;
        $orderOneIncrementId = 333;
        $orderDetailsOne = [
            'entity_id' => $orderOneEntityId,
            'increment_id' => $orderOneIncrementId
        ];

        $orderOne = $this->getMockOrder($orderOneEntityId, $orderOneIncrementId);

        $json1 = json_encode($orderDetailsOne);

        // Order One
        $orderTwoEntityId = 666;
        $orderTwoIncrementId = 777;
        $orderDetailsTwo = [
            'entity_id' => $orderTwoEntityId,
            'increment_id' => $orderTwoIncrementId
        ];

        $orderTwo = $this->getMockOrder($orderTwoEntityId, $orderTwoIncrementId);

        $json2 = json_encode($orderDetailsTwo);

        $this->json->expects($this->exactly(2))
            ->method('serialize')
            ->withConsecutive([$orderDetailsOne], [$orderDetailsTwo])
            ->will($this->onConsecutiveCalls($json1, $json2));

        $orders = [$orderOne, $orderTwo];

        $this->website->expects($this->once())
            ->method('getId')
            ->willReturn($websiteId);

        $this->helper->expects($this->once())
            ->method('getCreateableOrders')
            ->willReturn($orders);

        $this->requestBuilder->expects($this->once())
            ->method('setScopeId')
            ->with($websiteId);

        $this->requestBuilder->expects($this->exactly(2))
            ->method('addRequestLine')
            ->withConsecutive([$json1], [$json2]);

        $this->requestBuilder->expects($this->once())
            ->method('saveRequest')
            ->with(
                RequestInterface::TYPE_EXPORT,
                RequestInterface::ENTITY_ORDER,
                RequestInterface::OP_CREATE
            );

        $this->builder->build($this->website);
    }

    protected function getMockOrder($entityId, $incrementId)
    {
        $mockOrder = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockOrder->expects($this->once())
            ->method('getEntityId')
            ->willReturn($entityId);

        $mockOrder->expects($this->once())
            ->method('getIncrementId')
            ->willReturn($incrementId);

        return $mockOrder;
    }
}
