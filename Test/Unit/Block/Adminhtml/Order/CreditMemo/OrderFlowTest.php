<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Order\CreditMemo;

use Magento\Backend\Block\Template\Context;
use Magento\Sales\Block\Order\Creditmemo;
use Magento\Sales\Model\Order;
use PHPUnit\Framework\TestCase;
use Magento\Framework\Registry;
use Magento\Sales\Helper\Admin;
use RealtimeDespatch\OrderFlow\Helper\Admin\Info;
use RealtimeDespatch\OrderFlow\Helper\Api;
use RealtimeDespatch\OrderFlow\Block\Adminhtml\Order\CreditMemo\OrderFlow;

class OrderFlowTest extends TestCase
{
    protected $registry;
    protected $adminHelper;
    protected $infoHelper;
    protected $apiHelper;
    protected $block;

    public function setUp()
    {
        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->registry = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->adminHelper = $this->getMockBuilder(Admin::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->infoHelper = $this->getMockBuilder(Info::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->apiHelper = $this->getMockBuilder(Api::class)
            ->disableOriginalConstructor()
            ->getMock();

        $data = [];

        $this->block = new OrderFlow(
            $context,
            $this->registry,
            $this->adminHelper,
            $this->infoHelper,
            $this->apiHelper,
            $data
        );
    }

    public function testGetCreditMemo()
    {
        $order = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var Order\Creditmemo $creditMemo */
        $creditMemo = $this->getMockBuilder(Creditmemo::class)
            ->disableOriginalConstructor()
            ->getMock();

        $creditMemo
            ->expects($this->once())
            ->method('getOrder')
            ->willReturn($order);

        $this->registry
            ->expects($this->once())
            ->method('registry')
            ->with($this->equalTo('current_creditmemo'))
            ->willReturn($creditMemo);

        $this->assertSame($order, $this->block->getOrder());
    }

    /**
     * @depends testGetCreditMemo
     */
    public function testGetOrder()
    {
        $creditMemo = $this->getMockBuilder(Creditmemo::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->registry
            ->expects($this->once())
            ->method('registry')
            ->with('current_creditmemo')
            ->willReturn($creditMemo);

        $this->assertSame($creditMemo, $this->block->getCreditmemo());
    }

    /**
     * @depends testGetOrder
     */
    public function testGetOrderFlowOrderUrl()
    {
        $storeId = 1;
        $endpoint = 'https://www.test-endpoint.com/';
        $incrementId = '66666666';
        $channel = 'test_channel';

        $expectedUrl  = $endpoint.'despatch/order/referenceDetail.htm?externalReference=';
        $expectedUrl .= urlencode($incrementId);
        $expectedUrl .= '&channel='.urlencode($channel);

        $order = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->getMock();

        $order->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);

        $order->expects($this->once())
            ->method('getIncrementId')
            ->willReturn($incrementId);

        $this->apiHelper
            ->expects($this->once())
            ->method('getEndpoint')
            ->with($storeId)
            ->willReturn($endpoint);

        $this->apiHelper
            ->expects($this->once())
            ->method('getChannel')
            ->with($storeId)
            ->willReturn($channel);

        $creditMemo = $this->getMockBuilder(Creditmemo::class)
            ->disableOriginalConstructor()
            ->getMock();

        $creditMemo
            ->expects($this->once())
            ->method('getOrder')
            ->willReturn($order);

        $this->registry
            ->expects($this->once())
            ->method('registry')
            ->with('current_creditmemo')
            ->willReturn($creditMemo);

        $this->assertSame($expectedUrl, $this->block->getOrderFlowOrderUrl());
    }

    public function testCanDisplayAdminInfo()
    {
        $this->infoHelper
            ->expects($this->any())
            ->method("isEnabled")
            ->will($this->onConsecutiveCalls(true, false));

        $this->assertTrue($this->block->canDisplayAdminInfo());
        $this->assertFalse($this->block->canDisplayAdminInfo());
    }
}
