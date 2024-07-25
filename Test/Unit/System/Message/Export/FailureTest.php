<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\System\Message\Export;



use Magento\Framework\AuthorizationInterface;
use Magento\Framework\UrlInterface;
use RealtimeDespatch\OrderFlow\Api\Data\ExportInterface;
use RealtimeDespatch\OrderFlow\Model\Export;
use RealtimeDespatch\OrderFlow\Model\ExportFactory;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Export\Collection;
use RealtimeDespatch\OrderFlow\System\Message\Export\Failure;

class FailureTest extends \PHPUnit\Framework\TestCase
{
    protected Failure $failure;
    protected ExportFactory $mockExportFactory;
    protected AuthorizationInterface $mockAuthorization;
    protected UrlInterface $mockUrl;
    protected Export $mockExport;
    protected Collection $mockExportCollection;

    protected function setUp(): void
    {
        $this->mockExportFactory = $this->createMock(ExportFactory::class);
        $this->mockAuthorization = $this->createMock(AuthorizationInterface::class);
        $this->mockUrl = $this->createMock(UrlInterface::class);
        $this->mockExport = $this->createMock(Export::class);
        $this->mockExportCollection = $this->createMock(Collection::class);

        $this->mockExportFactory
            ->method('create')
            ->willReturn($this->mockExport);

        $this->mockExport
            ->method('getCollection')
            ->willReturn($this->mockExportCollection);

        $this->mockExportCollection
            ->expects($this->exactly(2))
            ->method('addFieldToFilter')
            ->withConsecutive(
                ['failures', ['gt' => 0]],
                ['viewed_at', ['null' => true]],
            )
            ->willReturnSelf();

        $this->mockExportCollection
            ->expects($this->once())
            ->method('setOrder')
            ->with('created_at')
            ->willReturnSelf();

        $this->mockExportCollection
            ->expects($this->once())
            ->method('setPageSize')
            ->with(1)
            ->willReturnSelf();

        $this->mockExportCollection
            ->expects($this->once())
            ->method('setCurPage')
            ->with(1)
            ->willReturnSelf();

        $this->mockExportCollection
            ->expects($this->once())
            ->method('getFirstItem')
            ->willReturn($this->mockExport);

        $this->failure = new Failure(
            $this->mockExportFactory,
            $this->mockAuthorization,
            $this->mockUrl,
        );
    }

    public function testGetIdentity(): void
    {
        $this->assertEquals('ORDERFLOW_EXPORT_FAILURE', $this->failure->getIdentity());
    }

    public function testGetText(): void
    {
        $exportId = rand(1, 10);

        $this->mockExport
            ->expects($this->once())
            ->method('getId')
            ->willReturn($exportId);

        $expectedUrl = 'http://example.com/admin/orderflow/export/view/export_id/' . $exportId;

        $this->mockUrl
            ->expects($this->once())
            ->method('getUrl')
            ->with(
                'orderflow/export/view',
                ['export_id' => $exportId]
            )
            ->willReturn($expectedUrl);


        $this->assertEquals(
            "A recent OrderFlow export contains failures. <a href=\"{$expectedUrl}\">View Details</a>",
            $this->failure->getText()
        );
    }

    public function testGetSeverity(): void
    {
        $this->assertEquals(2, $this->failure->getSeverity());
    }

    public function testIsDisplayedNotAllowed(): void
    {
        $this->mockAuthorization
            ->expects($this->once())
            ->method('isAllowed')
            ->with('RealtimeDespatch_OrderFlow::orderflow_exports')
            ->willReturn(false);

        $this->assertFalse($this->failure->isDisplayed());
    }

    public function testIsDisplayedNoUnreadExport(): void
    {
        $this->mockAuthorization
            ->expects($this->once())
            ->method('isAllowed')
            ->with('RealtimeDespatch_OrderFlow::orderflow_exports')
            ->willReturn(true);

        $this->assertFalse($this->failure->isDisplayed());
    }

    public function testIsDisplayed(): void
    {
        $this->mockAuthorization
            ->expects($this->once())
            ->method('isAllowed')
            ->with('RealtimeDespatch_OrderFlow::orderflow_exports')
            ->willReturn(true);

        $this->mockExport
            ->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $this->assertTrue($this->failure->isDisplayed());
    }
}