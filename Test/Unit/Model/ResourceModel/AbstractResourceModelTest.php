<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Adapter\Pdo\Mysql;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\ObjectRelationProcessor;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Model\ResourceModel\Db\Context;
use PHPUnit\Framework\MockObject\MockObject;

abstract class AbstractResourceModelTest extends \PHPUnit\Framework\TestCase
{
    protected AbstractDb $resource;
    protected Context $mockContext;
    protected ResourceConnection $mockResourceConnection;
    protected DateTime $mockDate;
    protected AdapterInterface $mockAdapter;
    protected Select $mockSelect;
    protected ObjectRelationProcessor $mockObjectRelationProcessor;

    protected function setUp(): void
    {
        $this->mockContext = $this->createMock(Context::class);
        $this->mockResourceConnection = $this->createMock(ResourceConnection::class);
        $this->mockObjectRelationProcessor = $this->createMock(ObjectRelationProcessor::class);

        $this->mockContext
            ->method('getResources')
            ->willReturn($this->mockResourceConnection);

        $this->mockContext
            ->method('getObjectRelationProcessor')
            ->willReturn($this->mockObjectRelationProcessor);

        $this->mockResourceConnection
            ->method('getTableName')
            ->with($this->getMainTableName())
            ->willReturn($this->getMainTableName());

        $this->mockAdapter = $this->createMock(Mysql::class);

        $this->mockResourceConnection
            ->method('getConnection')
            ->with('default')
            ->willReturn($this->mockAdapter);

        $this->mockSelect = $this->createMock(Select::class);

        $this->mockAdapter
            ->method('select')
            ->willReturn($this->mockSelect);

        $this->mockAdapter
            ->method('getTableName')
            ->with($this->getMainTableName())
            ->willReturn($this->getMainTableName());

        $this->mockDate = $this->createMock(DateTime::class);
        $this->resource = $this->getResource();
    }

    public function testGetIdByRequestId(): void
    {
        if (!method_exists($this->resource, 'getIdByRequestId')) {
            $this->markTestSkipped('Method getIdByRequestId does not exist');
        }

        $this->mockSelect
            ->expects($this->once())
            ->method('from')
            ->with($this->getMainTableName(), $this->getIdFieldName())
            ->willReturnSelf();

        $this->mockSelect
            ->expects($this->once())
            ->method('where')
            ->with("request_id = :request_id")
            ->willReturnSelf();

        $this->mockAdapter
            ->expects($this->once())
            ->method('fetchOne')
            ->with($this->mockSelect, [':request_id' => 1])
            ->willReturn(2);

        $result = $this->resource->getIdByRequestId(1);
        $this->assertEquals(2, $result);
    }

    public function testBeforeSave(): void
    {
        $mockModel = $this->getMockModel();

        $mockModel
            ->expects($this->once())
            ->method('hasDataChanges')
            ->willReturn(true);

        $mockModel
            ->expects($this->once())
            ->method('isSaveAllowed')
            ->willReturn(true);

        $mockModel
            ->expects($this->once())
            ->method('getData')
            ->willReturn([]);

        $this->mockObjectRelationProcessor
            ->expects($this->once())
            ->method('validateDataIntegrity')
            ->with($this->getMainTableName(), []);

        $this->mockAdapter
            ->expects($this->once())
            ->method('describeTable')
            ->with($this->getMainTableName())
            ->willReturn([]);

        $this->resource->save($mockModel);
    }

    abstract protected function getResource(): AbstractDb;
    abstract protected function getMockModel(): MockObject;
    abstract protected function getMainTableName(): string;
    abstract protected function getIdFieldName(): string;
}