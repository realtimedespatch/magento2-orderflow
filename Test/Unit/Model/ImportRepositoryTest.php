<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model;

use RealtimeDespatch\OrderFlow\Api\ImportRepositoryInterface;
use RealtimeDespatch\OrderFlow\Model\Import;
use RealtimeDespatch\OrderFlow\Model\ImportFactory;
use RealtimeDespatch\OrderFlow\Model\ImportLine;
use RealtimeDespatch\OrderFlow\Model\ImportLineFactory;
use RealtimeDespatch\OrderFlow\Model\ImportRepository;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Import as ImportResource;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\ImportLine\Collection as ImportLineCollection;

class ImportRepositoryTest extends \PHPUnit\Framework\TestCase
{
    protected Import $mockImport;
    protected ImportRepositoryInterface $importRepository;
    protected ImportResource $mockImportResource;
    protected ImportFactory $mockImportFactory;
    protected ImportLineFactory $mockImportLineFactory;
    protected ImportLineCollection $mockImportLineCollection;
    protected ImportLine $mockImportLine;

    protected function setUp(): void
    {
        $this->mockImport = $this->getMockBuilder(Import::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getId'])
            ->addMethods(['setLines'])
            ->getMock();

        $this->mockImportLine = $this->createMock(ImportLine::class);
        $this->mockImportResource = $this->createMock(ImportResource::class);
        $this->mockImportFactory = $this->createMock(ImportFactory::class);
        $this->mockImportLineFactory = $this->createMock(ImportLineFactory::class);
        $this->mockImportLineCollection = $this->createMock(ImportLineCollection::class);

        $this->importRepository = new ImportRepository(
            $this->mockImportResource,
            $this->mockImportFactory,
            $this->mockImportLineFactory,
        );
    }

    public function testGet(): void
    {
        $this->mockImportFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->mockImport);

        $this->mockImportLineFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->mockImportLine);

        $this->mockImportLine
            ->expects($this->once())
            ->method('getCollection')
            ->willReturn($this->mockImportLineCollection);

        $this->mockImportLineCollection
            ->expects($this->once())
            ->method('addFieldToSelect')
            ->with('*')
            ->willReturnSelf();

        $this->mockImportLineCollection
            ->expects($this->once())
            ->method('addFieldToFilter')
            ->with('import_id', ['eq' => 6])
            ->willReturnSelf();

        $this->mockImportLineCollection
            ->expects($this->once())
            ->method('loadData')
            ->willReturn([
                $this->mockImportLine,
            ]);

        $this->mockImport
            ->expects($this->once())
            ->method('setLines')
            ->with([$this->mockImportLine]);

        $this->mockImportResource
            ->expects($this->once())
            ->method('load')
            ->with($this->mockImport, 6);

        $this->mockImport
            ->expects($this->exactly(2))
            ->method('getId')
            ->willReturn(6);

        $this->assertEquals($this->mockImport, $this->importRepository->get(6));
    }

    public function testGetException(): void
    {
        $this->expectException(\Magento\Framework\Exception\NoSuchEntityException::class);
        $this->expectExceptionMessage('Import with id "6" does not exist.');

        $this->mockImportFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->mockImport);

        $this->mockImport
            ->expects($this->once())
            ->method('getId')
            ->willReturn(null);

        $this->importRepository->get(6);
    }

    public function testSave(): void
    {
        $this->mockImportResource
            ->expects($this->once())
            ->method('save')
            ->with($this->mockImport);

        $result = $this->importRepository->save($this->mockImport);
        $this->assertEquals($this->mockImport, $result);
    }

    public function testSaveException(): void
    {
        $this->expectException(\Magento\Framework\Exception\CouldNotSaveException::class);
        $this->expectExceptionMessage('An error occurred while saving the import.');

        $this->mockImportResource
            ->expects($this->once())
            ->method('save')
            ->willThrowException(new \Exception('An error occurred while saving the import.'));

        $this->importRepository->save($this->mockImport);
    }

    public function testDelete(): void
    {
        $this->mockImportResource
            ->expects($this->once())
            ->method('delete')
            ->with($this->mockImport);

        $this->assertTrue($this->importRepository->delete($this->mockImport));
    }

    public function testDeleteException(): void
    {
        $this->expectException(\Magento\Framework\Exception\CouldNotDeleteException::class);
        $this->expectExceptionMessage('An error occurred while deleting the import.');

        $this->mockImportResource
            ->expects($this->once())
            ->method('delete')
            ->willThrowException(new \Exception('An error occurred while deleting the import.'));

        $this->importRepository->delete($this->mockImport);
    }
}
