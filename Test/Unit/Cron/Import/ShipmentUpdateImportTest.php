<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Cron\Import;

use RealtimeDespatch\OrderFlow\Cron\Import\ShipmentUpdateImport;
use RealtimeDespatch\OrderFlow\Helper\Import\Shipment as ShipmentImportHelper;

class ShipmentUpdateImportTest extends AbstractImportCronTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->mockImportHelper = $this->createMock(ShipmentImportHelper::class);

        $this->importCron = new ShipmentUpdateImport(
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
            ->willReturn('Shipment');

        $this->mockRequest
            ->method('getOperation')
            ->willReturn('Update');

        if ($isEnabled) {
            $this->mockObjectManager
                ->expects($this->once())
                ->method('create')
                ->with('ShipmentUpdateRequestProcessor')
                ->willReturn($this->mockRequestProcessor);
        }

        parent::testExecute($isEnabled, $numEntities);
    }

    protected function getEntityType(): string
    {
        return 'Shipment';
    }
}