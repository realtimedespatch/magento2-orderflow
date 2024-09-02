<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model;

use RealtimeDespatch\OrderFlow\Api\ExportRepositoryInterface;
use RealtimeDespatch\OrderFlow\Model\Export;
use RealtimeDespatch\OrderFlow\Model\ExportFactory;
use RealtimeDespatch\OrderFlow\Model\ExportLine;
use RealtimeDespatch\OrderFlow\Model\ExportLineFactory;
use RealtimeDespatch\OrderFlow\Model\ExportRepository;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Export as ExportResource;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\ExportLine\Collection as ExportLineCollection;


class ExportRepositoryTest extends \PHPUnit\Framework\TestCase
{
    protected Export $mockExport;
    protected ExportRepositoryInterface $exportRepository;
    protected ExportResource $mockExportResource;
    protected ExportFactory $mockExportFactory;
    protected ExportLineFactory $mockExportLineFactory;
    protected ExportLineCollection $mockExportLineCollection;
    protected ExportLine $mockExportLine;

    protected function setUp(): void
    {
        $this->mockExport = $this->createMock(Export::class);
        $this->mockExportLine = $this->createMock(ExportLine::class);
        $this->mockExportResource = $this->createMock(ExportResource::class);
        $this->mockExportFactory = $this->createMock(ExportFactory::class);
        $this->mockExportLineFactory = $this->createMock(ExportLineFactory::class);
        $this->mockExportLineCollection = $this->createMock(ExportLineCollection::class);

        $this->exportRepository = new ExportRepository(
            $this->mockExportResource,
            $this->mockExportFactory,
            $this->mockExportLineFactory,
        );
    }

    public function testGet(): void
    {
        $this->mockExportFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->mockExport);

        $this->mockExportLineFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->mockExportLine);

        $this->mockExportLine
            ->expects($this->once())
            ->method('getCollection')
            ->willReturn($this->mockExportLineCollection);

        $this->mockExportLineCollection
            ->expects($this->once())
            ->method('addFieldToSelect')
            ->with('*')
            ->willReturnSelf();

        $this->mockExportLineCollection
            ->expects($this->once())
            ->method('addFieldToFilter')
            ->with('export_id', ['eq' => 6])
            ->willReturnSelf();

        $this->mockExportLineCollection
            ->expects($this->once())
            ->method('loadData')
            ->willReturn([
                $this->mockExportLine,
            ]);

        $this->mockExport
            ->expects($this->once())
            ->method('setLines')
            ->with([$this->mockExportLine]);

        $this->mockExportResource
            ->expects($this->once())
            ->method('load')
            ->with($this->mockExport, 6);

        $this->mockExport
            ->expects($this->exactly(2))
            ->method('getId')
            ->willReturn(6);

        $this->assertEquals($this->mockExport, $this->exportRepository->get(6));
    }

    public function testGetException(): void
    {
        $this->expectException(\Magento\Framework\Exception\NoSuchEntityException::class);
        $this->expectExceptionMessage('Export with id "6" does not exist.');

        $this->mockExportFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->mockExport);

        $this->mockExport
            ->expects($this->once())
            ->method('getId')
            ->willReturn(null);

        $this->exportRepository->get(6);
    }

    public function testSave(): void
    {
        $this->mockExportResource
            ->expects($this->once())
            ->method('save')
            ->with($this->mockExport);

        $result = $this->exportRepository->save($this->mockExport);
        $this->assertEquals($this->mockExport, $result);
    }

    public function testSaveException(): void
    {
        $this->expectException(\Magento\Framework\Exception\CouldNotSaveException::class);
        $this->expectExceptionMessage('An error occurred while saving the export.');

        $this->mockExportResource
            ->expects($this->once())
            ->method('save')
            ->willThrowException(new \Exception('An error occurred while saving the export.'));

        $this->exportRepository->save($this->mockExport);
    }

    public function testDelete(): void
    {
        $this->mockExportResource
            ->expects($this->once())
            ->method('delete')
            ->with($this->mockExport);

        $this->assertTrue($this->exportRepository->delete($this->mockExport));
    }

    public function testDeleteException(): void
    {
        $this->expectException(\Magento\Framework\Exception\CouldNotDeleteException::class);
        $this->expectExceptionMessage('An error occurred while deleting the export.');

        $this->mockExportResource
            ->expects($this->once())
            ->method('delete')
            ->willThrowException(new \Exception('An error occurred while deleting the export.'));

        $this->exportRepository->delete($this->mockExport);
    }
}