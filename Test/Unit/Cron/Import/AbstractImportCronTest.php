<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Cron\Import;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\ObjectManagerInterface;
use Psr\Log\LoggerInterface;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface;
use RealtimeDespatch\OrderFlow\Cron\Import\ImportCron;
use RealtimeDespatch\OrderFlow\Model\Builder\RequestBuilder;
use RealtimeDespatch\OrderFlow\Model\Request;
use RealtimeDespatch\OrderFlow\Model\RequestFactory;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Request\Collection;
use RealtimeDespatch\OrderFlow\Model\Service\Request\RequestProcessor;

abstract class AbstractImportCronTest extends \PHPUnit\Framework\TestCase
{
    protected ImportCron $importCron;
    protected LoggerInterface $mockLogger;
    protected RequestInterface $mockRequest;
    protected RequestFactory $mockRequestFactory;
    protected Collection $mockRequestCollection;
    protected ObjectManagerInterface $mockObjectManager;
    protected AbstractHelper $mockImportHelper;
    protected RequestProcessor $mockRequestProcessor;

    protected function setUp(): void
    {
        $this->mockLogger = $this->createMock(LoggerInterface::class);
        $this->mockRequestFactory = $this->createMock(RequestFactory::class);
        $this->mockObjectManager = $this->createMock(ObjectManagerInterface::class);
        $this->mockRequest = $this->createMock(Request::class);
        $this->mockRequestProcessor = $this->createMock(RequestProcessor::class);
        $this->mockRequestCollection = $this->createMock(Collection::class);
    }

    /**
     * @dataProvider testExecuteDataProvider
     * @return void
     */
    public function testExecute(
        bool $isEnabled,
        int $numEntities = 1
    ): void
    {
        $this->mockImportHelper
            ->expects($this->once())
            ->method('isEnabled')
            ->willReturn($isEnabled);

        if (!$isEnabled) {
            $this->mockRequestFactory
                ->expects($this->never())
                ->method('create');
        } else {
            $this->mockRequestFactory
                ->expects($this->once())
                ->method('create')
                ->willReturn($this->mockRequest);

            $this->mockRequest
                ->expects($this->once())
                ->method('getCollection')
                ->willReturn($this->mockRequestCollection);

            $this->mockRequestCollection
                ->expects($this->exactly(3))
                ->method('addFieldToFilter')
                ->withConsecutive(
                    ['type',  ['eq' => 'Import']],
                    ['entity', ['eq' => $this->getEntityType()]],
                    ['processed_at', ['null' => true]],
                )
                ->willReturnSelf();

            $this->mockRequestCollection
                ->expects($this->once())
                ->method('setOrder')
                ->with('message_id', 'ASC')
                ->willReturnSelf();

            $batchSize = rand(50, 100);

            $this->mockImportHelper
                ->expects($this->once())
                ->method('getBatchSize')
                ->willReturn($batchSize);

            $this->mockRequestCollection
                ->expects($this->once())
                ->method('setPageSize')
                ->with($batchSize)
                ->willReturnSelf();

            $this->mockRequestCollection
                ->expects($this->once())
                ->method('setCurPage')
                ->with(1)
                ->willReturn([$this->mockRequest]);



        }

        $this->importCron->execute();
    }

    public function testExecuteDataProvider(): array
    {
        return [
            [
                'isEnabled' => true,
                'numEntities' => 0,
            ],
            [
                'isEnabled' => true,
                'numEntities' => 1,
            ],
            [
                'isEnabled' => false,
            ]
        ];
    }

    abstract protected function getEntityType(): string;
}