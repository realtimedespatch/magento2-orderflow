<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\Service;

use RealtimeDespatch\OrderFlow\Model\QuantityItem;
use RealtimeDespatch\OrderFlow\Model\Request;
use RealtimeDespatch\OrderFlow\Model\SequenceItem;
use RealtimeDespatch\OrderFlow\Model\Service\InventoryRequestService;

class InventoryRequestServiceTest extends \PHPUnit\Framework\TestCase
{
    protected InventoryRequestService $inventoryRequestService;
    protected \Magento\Framework\Registry $mockRegistry;
    protected \Psr\Log\LoggerInterface $mockLogger;
    protected \RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface $mockRequestBuilder;
    protected \Magento\Framework\App\Request\Http $mockHttpRequest;
    protected Request $mockRequest;

    protected function setUp(): void
    {
        $this->mockRegistry = $this->createMock(\Magento\Framework\Registry::class);
        $this->mockLogger = $this->createMock(\Psr\Log\LoggerInterface::class);
        $this->mockRequestBuilder = $this->getMockBuilder(\RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveRequest'])
            ->addMethods(['setRequestBody', 'setRequestData', 'addRequestLine', 'getRequest'])
            ->getMock();
        $this->mockHttpRequest = $this->createMock(\Magento\Framework\App\Request\Http::class);
        $this->mockRequest = $this->createMock(Request::class);

        $this->inventoryRequestService = new InventoryRequestService(
            $this->mockRegistry,
            $this->mockLogger,
            $this->mockRequestBuilder,
            $this->mockHttpRequest
        );
    }

    public function testUpdate()
    {
        $skuQtys = [
            'SKU-001' => 10,
            'SKU-002' => 20,
        ];

        $lastHour = time() - 3600;
        $lastWeek = time() - 604800;

        $productQty = new QuantityItem();
        $productQty->setSku('SKU-001');
        $productQty->setQty(rand(1, 100));

        $seq = new SequenceItem();
        $seq->setSku('SKU-001');
        $seq->setLastOrderExported(date('Y-m-d H:i:s', rand($lastWeek, $lastHour)));
        $seq->setSeq(rand());

        $productQty2 = new QuantityItem();
        $productQty2->setSku('SKU-002');
        $productQty2->setQty(rand(1, 100));

        $seq2 = new SequenceItem();
        $seq2->setSku('SKU-002');
        $seq2->setLastOrderExported(date('Y-m-d H:i:s', rand($lastWeek, $lastHour)));
        $seq2->setSeq(2);

        $messageSeqId = 123;

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('setRequestData')
            ->with(
                'Import',
                'Inventory',
                'Update',
                $messageSeqId
            );

        $productQtyBody = (array) $productQty;
        $productQtyBody['lastOrderExported'] = $seq->getLastOrderExported();

        $productQtyBody2 = (array) $productQty2;
        $productQtyBody2['lastOrderExported'] = $seq2->getLastOrderExported();

        $this->mockRequestBuilder
            ->expects($this->exactly(2))
            ->method('addRequestLine')
            ->withConsecutive(
                [json_encode($productQtyBody), $seq->getSeq()],
                [json_encode($productQtyBody2), $seq2->getSeq()]
            );

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('saveRequest');

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('getRequest')
            ->willReturn($this->mockRequest);

        $this->mockRequest
            ->expects($this->once())
            ->method('getId')
            ->willReturn(123);

        $this->mockRegistry
            ->expects($this->once())
            ->method('register')
            ->with('request_id', 123);

        $this->inventoryRequestService->update(
            [$productQty, $productQty2],
            [$seq, $seq2],
            $messageSeqId
        );
    }
}