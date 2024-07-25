<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Cron\Import;

use RealtimeDespatch\OrderFlow\Cron\Import\InventoryUpdateImport;
use RealtimeDespatch\OrderFlow\Helper\Import\Inventory as InventoryImportHelper;

class InventoryUpdateImportTest extends AbstractImportCronTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->mockImportHelper = $this->createMock(InventoryImportHelper::class);

        $this->importCron = new InventoryUpdateImport(
            $this->mockImportHelper,
            $this->mockLogger,
            $this->mockRequestFactory,
            $this->mockObjectManager,
        );
    }

    /**
     * @dataProvider testExecuteDataProvider
     * @return void
     */
    public function testExecute(bool $isEnabled, int $numEntities = 1): void
    {
        $this->mockRequest
            ->method('getEntity')
            ->willReturn('Inventory');

        $this->mockRequest
            ->method('getOperation')
            ->willReturn('Update');

        if ($isEnabled) {
            $this->mockObjectManager
                ->expects($this->once())
                ->method('create')
                ->with('InventoryUpdateRequestProcessor')
                ->willReturn($this->mockRequestProcessor);
        }

        parent::testExecute($isEnabled, $numEntities);
    }

    protected function getEntityType(): string
    {
        return 'Inventory';
    }
}