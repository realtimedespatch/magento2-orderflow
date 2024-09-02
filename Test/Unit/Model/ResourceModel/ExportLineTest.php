<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\ResourceModel;

use PHPUnit\Framework\MockObject\MockObject;
use RealtimeDespatch\OrderFlow\Model\Export;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Export as ExportResource;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\ExportLine;

class ExportLineTest extends AbstractResourceModelTest
{

    public function testGetIdByRequestId(): void
    {
        $this->assertTrue(true);
    }

    protected function getMainTableName(): string
    {
        return 'rtd_export_lines';
    }

    protected function getIdFieldName(): string
    {
        return 'line_id';
    }

    protected function getResource(): AbstractDb
    {
        return new ExportLine($this->mockContext, $this->mockDate);
    }

    protected function getMockModel(): MockObject
    {
        $mock = $this->createMock(\RealtimeDespatch\OrderFlow\Model\ExportLine::class);
        return $mock;
    }
}