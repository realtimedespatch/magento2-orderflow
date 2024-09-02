<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\ResourceModel;

use PHPUnit\Framework\MockObject\MockObject;
use RealtimeDespatch\OrderFlow\Model\RequestLine;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\RequestLine as RequestLineResource;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class RequestLineTest extends AbstractResourceModelTest
{
    protected function getMainTableName(): string
    {
        return 'rtd_request_lines';
    }

    protected function getIdFieldName(): string
    {
        return 'line_id';
    }

    protected function getResource(): AbstractDb
    {
        return new RequestLineResource($this->mockContext, $this->mockDate);
    }

    protected function getMockModel(): MockObject
    {
        return $this->createMock(RequestLine::class);
    }
}