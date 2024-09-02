<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model;

use RealtimeDespatch\OrderFlow\Model\Import;

class ImportTest extends AbstractModelTest
{
    protected Import $import;

    protected function setUp(): void
    {
        parent::setUp();

        $this->idFieldName = 'import_id';

        $this->import = new Import(
            $this->mockContext,
            $this->mockRegistry,
            $this->mockResource,
            $this->mockResourceCollection
        );
    }

    public function testData(): void
    {
        $this->import->setData('import_id', 1);
        $this->assertEquals(1, $this->import->getId());

        $this->import->setRequestId(2);
        $this->assertEquals(2, $this->import->getRequestId());

        $this->import->setMessageId(3);
        $this->assertEquals(3, $this->import->getMessageId());

        $this->import->setEntity('Order');
        $this->assertEquals('Order', $this->import->getEntity());

        $this->import->setOperation('Create');
        $this->assertEquals('Create', $this->import->getOperation());

        $this->import->setSuccesses(4);
        $this->assertEquals(4, $this->import->getSuccesses());

        $this->import->setFailures(5);
        $this->assertEquals(5, $this->import->getFailures());

        $this->import->setDuplicates(6);
        $this->assertEquals(6, $this->import->getDuplicates());

        $this->import->setSuperseded(7);
        $this->assertEquals(7, $this->import->getSuperseded());

        $this->import->setCreatedAt('2021-01-01 00:00:00');
        $this->assertEquals('2021-01-01 00:00:00', $this->import->getCreatedAt());

        $this->import->setViewedAt('2022-01-01 00:00:00');
        $this->assertEquals('2022-01-01 00:00:00', $this->import->getViewedAt());
    }
}