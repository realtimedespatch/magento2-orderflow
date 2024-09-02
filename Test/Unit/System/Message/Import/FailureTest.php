<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\System\Message\Import;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\UrlInterface;
use RealtimeDespatch\OrderFlow\Api\Data\ImportInterface;
use RealtimeDespatch\OrderFlow\Model\Import;
use RealtimeDespatch\OrderFlow\Model\ImportFactory;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Import\Collection;
use RealtimeDespatch\OrderFlow\System\Message\Import\Failure;

class FailureTest extends \PHPUnit\Framework\TestCase
{
    protected Failure $failure;
    protected ImportFactory $mockImportFactory;
    protected AuthorizationInterface $mockAuthorization;
    protected UrlInterface $mockUrl;
    protected Import $mockImport;
    protected Collection $mockImportCollection;

    protected function setUp(): void
    {
        $this->mockImportFactory = $this->createMock(ImportFactory::class);
        $this->mockAuthorization = $this->createMock(AuthorizationInterface::class);
        $this->mockUrl = $this->createMock(UrlInterface::class);
        $this->mockImport = $this->createMock(Import::class);
        $this->mockImportCollection = $this->createMock(Collection::class);

        $this->mockImportFactory
            ->method('create')
            ->willReturn($this->mockImport);

        $this->mockImport
            ->method('getCollection')
            ->willReturn($this->mockImportCollection);

        $this->mockImportCollection
            ->expects($this->exactly(2))
            ->method('addFieldToFilter')
            ->withConsecutive(
                ['failures', ['gt' => 0]],
                ['viewed_at', ['null' => true]],
            )
            ->willReturnSelf();

        $this->mockImportCollection
            ->expects($this->once())
            ->method('setOrder')
            ->with('created_at')
            ->willReturnSelf();

        $this->mockImportCollection
            ->expects($this->once())
            ->method('setPageSize')
            ->with(1)
            ->willReturnSelf();

        $this->mockImportCollection
            ->expects($this->once())
            ->method('setCurPage')
            ->with(1)
            ->willReturnSelf();

        $this->mockImportCollection
            ->expects($this->once())
            ->method('getFirstItem')
            ->willReturn($this->mockImport);

        $this->failure = new Failure(
            $this->mockImportFactory,
            $this->mockAuthorization,
            $this->mockUrl,
        );
    }

    public function testGetIdentity(): void
    {
        $this->assertEquals('ORDERFLOW_IMPORT_FAILURE', $this->failure->getIdentity());
    }

    public function testGetText(): void
    {
        $importId = rand(1, 10);

        $this->mockImport
            ->expects($this->once())
            ->method('getId')
            ->willReturn($importId);

        $expectedUrl = 'http://example.com/admin/orderflow/import/view/import_id/' . $importId;

        $this->mockUrl
            ->expects($this->once())
            ->method('getUrl')
            ->with(
                'orderflow/import/view',
                ['import_id' => $importId]
            )
            ->willReturn($expectedUrl);


        $this->assertEquals(
            "A recent OrderFlow import contains failures. <a href=\"{$expectedUrl}\">View Details</a>",
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
            ->with('RealtimeDespatch_OrderFlow::orderflow_imports')
            ->willReturn(false);

        $this->assertFalse($this->failure->isDisplayed());
    }

    public function testIsDisplayedNoUnreadImport(): void
    {
        $this->mockAuthorization
            ->expects($this->once())
            ->method('isAllowed')
            ->with('RealtimeDespatch_OrderFlow::orderflow_imports')
            ->willReturn(true);

        $this->assertFalse($this->failure->isDisplayed());
    }

    public function testIsDisplayed(): void
    {
        $this->mockAuthorization
            ->expects($this->once())
            ->method('isAllowed')
            ->with('RealtimeDespatch_OrderFlow::orderflow_imports')
            ->willReturn(true);

        $this->mockImport
            ->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $this->assertTrue($this->failure->isDisplayed());
    }
}