<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\ResourceModel;

use PHPUnit\Framework\MockObject\MockObject;
use RealtimeDespatch\OrderFlow\Model\Request;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Request as RequestResource;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class RequestTest extends AbstractResourceModelTest
{
    protected function getMainTableName(): string
    {
        return 'rtd_requests';
    }

    protected function getIdFieldName(): string
    {
        return 'request_id';
    }

    protected function getResource(): AbstractDb
    {
        return new RequestResource($this->mockContext, $this->mockDate);
    }

    protected function getMockModel(): MockObject
    {
        return $this->createMock(Request::class);
    }
}