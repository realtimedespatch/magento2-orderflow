<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\Service\Import\Type;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\ObjectManagerInterface;
use Psr\Log\LoggerInterface;
use RealtimeDespatch\OrderFlow\Model\ImportLine;
use RealtimeDespatch\OrderFlow\Model\RequestLine;

abstract class AbstractImporterTypeTest extends \PHPUnit\Framework\TestCase
{
    protected ScopeConfigInterface $mockScopeConfig;
    protected \RealtimeDespatch\OrderFlow\Model\Service\Import\Type\ImporterType $type;
    protected LoggerInterface $mockLogger;
    protected ObjectManagerInterface $mockObjectManager;
    protected \Magento\Framework\DB\Transaction $mockTxn;
    protected \RealtimeDespatch\OrderFlow\Model\Import $mockImport;
    protected RequestLine $mockRequestLine;
    protected \RealtimeDespatch\OrderFlow\Model\Request $mockRequest;
    protected \RealtimeDespatch\OrderFlow\Model\ImportLine $mockImportLine;
    protected \RealtimeDespatch\OrderFlow\Model\ResourceModel\ImportLine\Collection $mockImportLineCollection;

    protected function setUp(): void
    {
        $this->mockScopeConfig = $this->createMock(ScopeConfigInterface::class);
        $this->mockLogger = $this->createMock(LoggerInterface::class);
        $this->mockObjectManager = $this->createMock(ObjectManagerInterface::class);
        $this->mockTxn = $this->createMock(\Magento\Framework\DB\Transaction::class);
        $this->mockImport = $this->createMock(\RealtimeDespatch\OrderFlow\Model\Import::class);
        $this->mockRequestLine = $this->createMock(RequestLine::class);
        $this->mockRequest = $this->createMock(\RealtimeDespatch\OrderFlow\Model\Request::class);
        $this->mockImportLine = $this->createMock(\RealtimeDespatch\OrderFlow\Model\ImportLine::class);
        $this->mockImportLineCollection = $this->createMock(\RealtimeDespatch\OrderFlow\Model\ResourceModel\ImportLine\Collection::class);

        $this->mockRequestLine->method('getBody')->willReturn($this->getTestImportRequestLineBody());

        $this->mockRequest
            ->method('getLines')
            ->willReturn([
                $this->mockRequestLine
            ]);

        $this->mockImportLine
            ->method('getCollection')
            ->willReturn($this->mockImportLineCollection);

        $this->mockImportLineCollection
            ->method('addFieldToFilter')
            ->willReturnSelf();

        $this->mockImportLineCollection
            ->method('setOrder')
            ->willReturnSelf();

        $this->mockImportLineCollection
            ->method('getFirstItem')
            ->willReturn($this->mockImportLine);

        $this->mockObjectManager
            ->method('create')
            ->will($this->returnCallback(function($class) {
                if ($class == 'RealtimeDespatch\OrderFlow\Model\Import') {
                    return $this->mockImport;
                }
                if ($class == \Magento\Framework\DB\Transaction::class) {
                    return $this->mockTxn;
                }
                if ($class == 'RealtimeDespatch\OrderFlow\Model\ImportLine') {
                    return $this->mockImportLine;
                }
                if ($class == \RealtimeDespatch\OrderFlow\Model\ImportLine::class) {
                    return $this->mockImportLine;
                }
                if ($class == \RealtimeDespatch\OrderFlow\Model\ResourceModel\ImportLine\Collection::class) {
                    return $this->mockImportLineCollection;
                }
                return null;
            }));

    }

    public function testIsEnabled(): void
    {
        $this->mockScopeConfig
            ->method('getValue')
            ->with(
                $this->getEnabledXmlPath(),
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
            )
            ->willReturnOnConsecutiveCalls(
                true,
                false
            );

        $this->assertTrue($this->type->isEnabled());
        $this->assertFalse($this->type->isEnabled());
    }

    public function testGetType(): void
    {
        $this->assertEquals($this->getTypeName(), $this->type->getType());
    }

    public function testImport(): void
    {
        $this->mockTxn->expects($this->once())->method('save');
        $this->mockImport->expects($this->exactly(2))->method('setSuccesses')->withConsecutive([0], [1]);
        $this->mockImportLine->expects($this->exactly(1))->method('setResult')->withConsecutive(['Success']);
        $result = $this->type->import($this->mockRequest);
    }

    public function testImportDuplicate(): void
    {
        $this->mockImportLine->method('getId')->willReturn(1);
        $this->mockImport->expects($this->exactly(2))->method('setDuplicates')->withConsecutive([0], [1]);
        $result = $this->type->import($this->mockRequest);
    }

    public function testImportSuperseded(): void
    {
        $this->mockImport->method('getId')->willReturn(1);
        $this->mockImportLine->method('getSequenceId')->willReturn(1);

        $newerMockImportLine = $this->createMock(\RealtimeDespatch\OrderFlow\Model\ImportLine::class);
        $newerMockImportLine->method('getSequenceId')->willReturn(2);

        $this->mockImportLineCollection
            ->method('getFirstItem')
            ->willReturnOnConsecutiveCalls(
                $this->mockImportLine,
                $newerMockImportLine
            );

        $this->mockImport->expects($this->exactly(2))->method('setSuperseded')->withConsecutive([0], [1]);
        $result = $this->type->import($this->mockRequest);
    }

    abstract protected function getTestImportRequestLineBody(): object;
    abstract protected function getEnabledXmlPath(): string;
    abstract protected function getTypeName(): string;
}