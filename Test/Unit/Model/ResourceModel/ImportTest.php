<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\ResourceModel;

use PHPUnit\Framework\MockObject\MockObject;
use RealtimeDespatch\OrderFlow\Model\Export;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Export as ExportResource;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Import;
use RealtimeDespatch\OrderFlow\Model\Import as ImportModel;

class ImportTest extends AbstractResourceModelTest
{
    protected function getMainTableName(): string
    {
        return 'rtd_imports';
    }

    protected function getIdFieldName(): string
    {
        return 'import_id';
    }

    protected function getResource(): AbstractDb
    {
        return new Import($this->mockContext, $this->mockDate);
    }

    protected function getMockModel(): MockObject
    {
        return $this->createMock(ImportModel::class);
    }
}