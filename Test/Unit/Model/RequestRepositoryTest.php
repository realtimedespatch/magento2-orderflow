<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use RealtimeDespatch\OrderFlow\Api\RequestRepositoryInterface;
use RealtimeDespatch\OrderFlow\Model\Request;
use RealtimeDespatch\OrderFlow\Model\RequestFactory;
use RealtimeDespatch\OrderFlow\Model\RequestLine;
use RealtimeDespatch\OrderFlow\Model\RequestLineFactory;
use RealtimeDespatch\OrderFlow\Model\RequestRepository;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Request as RequestResource;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\RequestLine as RequestLineResource;

class RequestRepositoryTest extends \PHPUnit\Framework\TestCase
{
    protected RequestRepositoryInterface $requestRepository;
    protected RequestResource $mockRequestResource;
    protected RequestLineResource $mockRequestLineResource;
    protected RequestFactory $mockRequestFactory;
    protected RequestLineFactory $mockRequestLineFactory;
    protected Request $mockRequest;
    protected RequestLine $mockRequestLine;
    protected RequestLineResource\Collection $mockRequestLineCollection;

    protected function setUp(): void
    {
        $this->mockRequestResource = $this->createMock(RequestResource::class);
        $this->mockRequestLineResource = $this->createMock(RequestLineResource::class);
        $this->mockRequestFactory = $this->createMock(RequestFactory::class);
        $this->mockRequestLineFactory = $this->createMock(RequestLineFactory::class);
        $this->mockRequest = $this->createMock(Request::class);
        $this->mockRequestLine = $this->createMock(RequestLine::class);
        $this->mockRequestLineCollection = $this->createMock(RequestLineResource\Collection::class);

        $this->requestRepository = new RequestRepository(
            $this->mockRequestResource,
            $this->mockRequestLineResource,
            $this->mockRequestFactory,
            $this->mockRequestLineFactory
        );
    }

    public function testGet(): void
    {
        $this->mockRequestFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->mockRequest);

        $this->mockRequestResource
            ->expects($this->once())
            ->method('load')
            ->with($this->mockRequest, 6);

        $this->mockRequest
            ->expects($this->exactly(2))
            ->method('getId')
            ->willReturn(6);

        $this->mockRequestLineFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->mockRequestLine);

        $this->mockRequestLine
            ->expects($this->once())
            ->method('getCollection')
            ->willReturn($this->mockRequestLineCollection);

        $this->mockRequestLineCollection
            ->expects($this->once())
            ->method('addFieldToSelect')
            ->with('*')
            ->willReturnSelf();

        $this->mockRequestLineCollection
            ->expects($this->once())
            ->method('addFieldToFilter')
            ->with('request_id', ['eq' => 6])
            ->willReturnSelf();

        $this->mockRequestLineCollection
            ->expects($this->once())
            ->method('loadData')
            ->willReturn([
                $this->mockRequestLine,
            ]);

        $this->mockRequest
            ->expects($this->once())
            ->method('setLines')
            ->with([$this->mockRequestLine]);

        $result = $this->requestRepository->get(6);
        $this->assertEquals($this->mockRequest, $result);
    }

    public function testGetNotFound(): void
    {
        $this->mockRequestFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->mockRequest);

        $this->mockRequestResource
            ->expects($this->once())
            ->method('load')
            ->with($this->mockRequest, 7)
            ->willReturnSelf();

        $this->mockRequest
            ->expects($this->any())
            ->method('getId')
            ->willReturn(null);

        $this->expectException(NoSuchEntityException::class);
        $this->requestRepository->get(7);
    }

    public function testSave(): void
    {
        $this->mockRequestResource
            ->expects($this->once())
            ->method('save')
            ->with($this->mockRequest);

        $this->mockRequest
            ->expects($this->once())
            ->method('getLines')
            ->willReturn([
                $this->mockRequestLine,
            ]);

        $this->mockRequestLineResource
            ->expects($this->once())
            ->method('save')
            ->with($this->mockRequestLine);

        $result = $this->requestRepository->save($this->mockRequest);
        $this->assertEquals($this->mockRequest, $result);
    }

    public function testSaveException(): void
    {
        $this->mockRequestResource
            ->expects($this->once())
            ->method('save')
            ->with($this->mockRequest)
            ->willThrowException(new \Exception('Test message'));

        $this->expectException(CouldNotSaveException::class);
        $this->expectExceptionMessage('Test message');

        $this->requestRepository->save($this->mockRequest);
    }

    public function testDelete(): void
    {
        $this->mockRequestResource
            ->expects($this->once())
            ->method('delete')
            ->with($this->mockRequest);

        $result = $this->requestRepository->delete($this->mockRequest);
        $this->assertTrue($result);
    }

    public function testDeleteException(): void
    {
        $this->mockRequestResource
            ->expects($this->once())
            ->method('delete')
            ->with($this->mockRequest)
            ->willThrowException(new \Exception('Test message'));

        $this->expectException(CouldNotDeleteException::class);
        $this->expectExceptionMessage('Test message');
        $this->requestRepository->delete($this->mockRequest);
    }

}