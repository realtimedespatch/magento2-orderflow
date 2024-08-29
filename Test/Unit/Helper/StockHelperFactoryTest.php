<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Helper;

use Magento\Framework\App\Helper\Context;
use RealtimeDespatch\OrderFlow\Helper\Stock\LegacyStockHelperFactory;
use RealtimeDespatch\OrderFlow\Helper\Stock\LegacyStockHelper;
use RealtimeDespatch\OrderFlow\Helper\Stock\MsiStockHelperFactory;
use RealtimeDespatch\OrderFlow\Helper\Stock\MsiStockHelper;
use RealtimeDespatch\OrderFlow\Helper\StockHelperFactory;
use Magento\Framework\Module\Manager;

class StockHelperFactoryTest extends \PHPUnit\Framework\TestCase
{
    protected StockHelperFactory $stockHelperFactory;
    protected MsiStockHelperFactory $mockMsiStockHelperFactory;
    protected MsiStockHelper $mockMsiStockHelper;
    protected LegacyStockHelperFactory $mockLegacyStockHelperFactory;
    protected LegacyStockHelper $mockLegacyStockHelper;
    protected Context $mockContext;
    protected Manager $mockModuleManager;


    protected function setUp(): void
    {
        $this->mockContext = $this->createMock(Context::class);
        $this->mockMsiStockHelperFactory = $this->createMock(MsiStockHelperFactory::class);
        $this->mockMsiStockHelper = $this->createMock(MsiStockHelper::class);
        $this->mockLegacyStockHelperFactory = $this->createMock(LegacyStockHelperFactory::class);
        $this->mockLegacyStockHelper = $this->createMock(LegacyStockHelper::class);
        $this->mockModuleManager = $this->createMock(Manager::class);

        $this->mockMsiStockHelperFactory
            ->method('create')
            ->willReturn($this->mockMsiStockHelper);

        $this->mockLegacyStockHelperFactory
            ->method('create')
            ->willReturn($this->mockLegacyStockHelper);

        $this->stockHelperFactory = new StockHelperFactory(
            $this->mockMsiStockHelperFactory,
            $this->mockLegacyStockHelperFactory,
            $this->mockModuleManager
        );
    }

    public function testCreate(): void
    {
        $this->mockModuleManager
            ->method('isEnabled')
            ->with('Magento_InventoryApi')
            ->willReturnOnConsecutiveCalls([
                true,
                false,
            ]);

        $this->assertInstanceOf(MsiStockHelper::class, $this->stockHelperFactory->create());
        $this->assertInstanceOf(LegacyStockHelper::class, $this->stockHelperFactory->create());
    }
}