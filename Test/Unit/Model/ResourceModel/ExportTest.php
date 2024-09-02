<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\ResourceModel;

use PHPUnit\Framework\MockObject\MockObject;
use RealtimeDespatch\OrderFlow\Model\Export;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Export as ExportResource;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ExportTest extends AbstractResourceModelTest
{
    protected function getMainTableName(): string
    {
        return 'rtd_exports';
    }

    protected function getIdFieldName(): string
    {
        return 'export_id';
    }

    protected function getResource(): AbstractDb
    {
        return new ExportResource($this->mockContext, $this->mockDate);
    }

    protected function getMockModel(): MockObject
    {
        return $this->createMock(Export::class);
    }
}