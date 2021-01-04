<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Factory;

use Magento\Framework\ObjectManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use RealtimeDespatch\OrderFlow\Factory\RequestProcessorFactory;

class RequestProcessorFactoryTest extends TestCase
{
    /**
     * @var MockObject
     */
    protected $objectManager;

    /**
     * @var RequestProcessorFactory
     */
    protected $factory;

    public function setUp()
    {
        $this->objectManager = $this->getMockBuilder(ObjectManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->factory = new RequestProcessorFactory($this->objectManager);
    }

    public function testGet()
    {
        $entity = 'Shipment';
        $operation = 'Create';
        $className = 'ShipmentCreateRequestProcessor';

        $request = $this->getMockBuilder(RequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $request->expects($this->once())
            ->method('getEntity')
            ->willReturn($entity);

        $request->expects($this->once())
            ->method('getOperation')
            ->willReturn($operation);

        $requestProcessor = $this->getMockBuilder(ShipmentCreateRequestProcessor::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->objectManager->expects($this->once())
            ->method('create')
            ->with($className)
            ->willReturn($requestProcessor);

        $this->assertEquals($requestProcessor, $this->factory->get($request));
    }
}
