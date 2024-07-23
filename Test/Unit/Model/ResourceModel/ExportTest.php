<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Stdlib\DateTime\DateTime;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Export as ExportResource;
use Magento\Framework\Model\ResourceModel\Db\Context;

class ExportTest extends \PHPUnit\Framework\TestCase
{
    protected ExportResource $exportResource;
    protected Context $mockContext;
    protected ResourceConnection $mockResourceConnection;
    protected DateTime $mockDate;
    protected AdapterInterface $mockAdapter;
    protected Select $mockSelect;

    protected string $mainTable;
    protected string $idFieldName;

    protected function setUp(): void
    {
        $this->mainTable = 'rtd_exports';
        $this->idFieldName = 'export_id';
        $this->mockContext = $this->createMock(Context::class);
        $this->mockResourceConnection = $this->createMock(ResourceConnection::class);

        $this->mockContext
            ->method('getResources')
            ->willReturn($this->mockResourceConnection);

        $this->mockResourceConnection
            ->method('getTableName')
            ->with($this->mainTable)
            ->willReturn($this->mainTable);

        $this->mockAdapter = $this->createMock(AdapterInterface::class);
        $this->mockResourceConnection
            ->method('getConnection')
            ->with('default')
            ->willReturn($this->mockAdapter);

        $this->mockSelect = $this->createMock(Select::class);

        $this->mockAdapter
            ->method('select')
            ->willReturn($this->mockSelect);


        $this->mockDate = $this->createMock(DateTime::class);
        $this->exportResource = new ExportResource(
            $this->mockContext,
            $this->mockDate,
        );
    }

    public function testConstruct(): void
    {
        $this->assertEquals($this->mainTable, $this->exportResource->getMainTable());
        $this->assertEquals($this->idFieldName, $this->exportResource->getIdFieldName());
    }

    public function testGetIdByRequestId(): void
    {

        $this->mockSelect
            ->expects($this->once())
            ->method('from')
            ->with($this->mainTable, $this->idFieldName)
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

        $result = $this->exportResource->getIdByRequestId(1);
        $this->assertEquals(2, $result);
    }
}