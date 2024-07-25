<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Observer;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\DB\Transaction;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use RealtimeDespatch\OrderFlow\Model\Export;
use RealtimeDespatch\OrderFlow\Model\ExportLine;
use RealtimeDespatch\OrderFlow\Observer\OrderExport;
use RealtimeDespatch\OrderFlow\Observer\ProductExport;

class ProductExportTest extends \PHPUnit\Framework\TestCase
{
    protected ProductExport $productExport;
    protected Transaction $mockTxn;
    protected ProductRepositoryInterface $mockProductRepository;
    protected Product $mockProduct;
    protected Observer $mockObserver;
    protected Export $mockExport;

    protected function setUp(): void
    {
        $this->mockTxn = $this->createMock(Transaction::class);
        $this->mockProduct = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getId'])
            ->addMethods(['setOrderflowExportStatus'])
            ->getMock();
        $this->mockObserver = $this->createMock(Observer::class);
        $this->mockExport = $this->createMock(Export::class);
        $this->mockProductRepository = $this->createMock(ProductRepository::class);

        $this->productExport = new ProductExport(
            $this->mockTxn,
            $this->mockProductRepository
        );
    }

    public function testExecuteIgnore(): void
    {
        $this->mockObserver
            ->expects($this->once())
            ->method('getData')
            ->with('export')
            ->willReturn($this->mockExport);

        $this->mockExport
            ->expects($this->once())
            ->method('isProductExport')
            ->willReturn(false);

        $this->mockExport
            ->expects($this->never())
            ->method('getLines');

        $this->mockTxn
            ->expects($this->never())
            ->method('save');

        $this->productExport->execute($this->mockObserver);
    }

    public function testExecute(): void
    {
        $this->mockObserver
            ->expects($this->once())
            ->method('getData')
            ->with('export')
            ->willReturn($this->mockExport);

        $this->mockExport
            ->expects($this->once())
            ->method('isProductExport')
            ->willReturn(true);

        $this->mockExport
            ->expects($this->once())
            ->method('getLines')
            ->willReturn($this->getMockExportLines());

        $this->mockProductRepository
            ->expects($this->once())
            ->method('get')
            ->with('SKU-123')
            ->willReturn($this->mockProduct);

        $this->mockProduct
            ->method('getId')
            ->willReturn(1);

        $this->mockProduct
            ->expects($this->once())
            ->method('setOrderflowExportStatus')
            ->with('exported');

        $this->mockTxn
            ->expects($this->once())
            ->method('save');

        $this->productExport->execute($this->mockObserver);
    }

    public function testExecuteProductNotFound(): void
    {
        $this->mockObserver
            ->expects($this->once())
            ->method('getData')
            ->with('export')
            ->willReturn($this->mockExport);

        $this->mockExport
            ->expects($this->once())
            ->method('isProductExport')
            ->willReturn(true);

        $this->mockExport
            ->expects($this->once())
            ->method('getLines')
            ->willReturn($this->getMockExportLines());

        $this->mockProductRepository
            ->method('get')
            ->with('SKU-123')
            ->willThrowException(new NoSuchEntityException());

        $this->mockProduct
            ->method('getId')
            ->willReturn(null);

        $this->mockProduct
            ->expects($this->never())
            ->method('setOrderflowExportStatus');

        $this->mockTxn
            ->expects($this->once())
            ->method('save');

        $this->productExport->execute($this->mockObserver);
    }

    protected function getMockExportLines(): array
    {
        $mockExportLine1 = $this->createMock(ExportLine::class);
        $mockExportLine1
            ->method('getReference')
            ->willReturn('SKU-123');
        $mockExportLine1
            ->method('getEntityExportStatus')
            ->willReturn('exported');

        return [
            $mockExportLine1,
        ];
    }
}