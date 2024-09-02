<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Cron\Export;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\Website;
use Magento\Store\Model\WebsiteFactory;
use Psr\Log\LoggerInterface;
use RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use RealtimeDespatch\OrderFlow\Cron\Export\ExportCron;
use RealtimeDespatch\OrderFlow\Model\Builder\RequestBuilder;
use RealtimeDespatch\OrderFlow\Model\Request;
use RealtimeDespatch\OrderFlow\Model\Service\Request\RequestProcessor;

abstract class AbstractExportCronTest extends \PHPUnit\Framework\TestCase
{
    protected ExportCron $exportCron;
    protected LoggerInterface $mockLogger;
    protected RequestBuilderInterface $mockRequestBuilder;
    protected RequestInterface $mockRequest;
    protected ObjectManagerInterface $mockObjectManager;
    protected WebsiteFactory $mockWebsiteFactory;
    protected Website $mockWebsite;
    protected AbstractHelper $mockExportHelper;
    protected RequestProcessor $mockRequestProcessor;

    protected function setUp(): void
    {
        $this->mockLogger = $this->createMock(LoggerInterface::class);
        $this->mockRequestBuilder = $this->createMock(RequestBuilder::class);
        $this->mockObjectManager = $this->createMock(ObjectManagerInterface::class);
        $this->mockWebsiteFactory = $this->createMock(WebsiteFactory::class);
        $this->mockWebsite = $this->createMock(Website::class);
        $this->mockRequest = $this->createMock(Request::class);
        $this->mockRequestProcessor = $this->createMock(RequestProcessor::class);

        $this->mockWebsite
            ->method('getId')
            ->willReturn(1);

        $this->mockRequestBuilder
            ->method('resetBuilder')
            ->willReturnSelf();
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
        $this->mockWebsiteFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->mockWebsite);

        $this->mockWebsite
            ->expects($this->once())
            ->method('getCollection')
            ->willReturn([$this->mockWebsite]);

        if ($isEnabled && $numEntities > 0) {
            $this->mockRequestBuilder
                ->expects($this->once())
                ->method('setScopeId')
                ->with(1);

            $this->mockRequestBuilder
                ->expects($this->once())
                ->method('saveRequest')
                ->willReturn($this->mockRequest);
        } else {

        }

        $this->exportCron->execute();
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

    protected function getExportableEntities(int $n): array
    {
        $entities = [];
        for ($i = 0; $i < $n; $i++) {
            $entities[] = $this->getMockEntity();
        }
        return $entities;
    }

    abstract protected function getMockEntity(): AbstractModel;
}