<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Helper\Import;

use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Helper\Import\Inventory;
use RealtimeDespatch\OrderFlow\Helper\Import\Shipment;


class InventoryTest extends TestCase
{
    protected Inventory $inventoryHelper;
    protected ScopeConfigInterface $mockScopeConfig;

    protected function setUp(): void
    {
        $mockContext = $this->createMock(\Magento\Framework\App\Helper\Context::class);

        $this->mockScopeConfig = $this->createMock(\Magento\Framework\App\Config\ScopeConfigInterface::class);
        $mockContext->method('getScopeConfig')->willReturn($this->mockScopeConfig);

        $this->inventoryHelper = new Inventory(
            $mockContext
        );

        parent::setUp();
    }

    /**
     * @dataProvider boolDataProvider
     */
    public function testIsEnabled($enabled): void
    {
        $this->mockScopeConfig
            ->method('isSetFlag')
            ->with('orderflow_inventory_import/settings/is_enabled')
            ->willReturn($enabled);

        $this->assertEquals($enabled, $this->inventoryHelper->isEnabled());
    }

    public function testGetBatchSize(): void
    {
        $batchSize = rand(1, 100);

        $this->mockScopeConfig
            ->method('getValue')
            ->with('orderflow_inventory_import/settings/batch_size')
            ->willReturn($batchSize);

        $this->assertEquals($batchSize, $this->inventoryHelper->getBatchSize());
    }

    /**
     * @dataProvider boolDataProvider
     * @param bool $enabled
     * @return void
     */
    public function testIsNegativeQtyEnabled(bool $enabled): void
    {
        $this->mockScopeConfig
            ->method('isSetFlag')
            ->with('orderflow_inventory_import/settings/negative_qtys_enabled')
            ->willReturn($enabled);

        $this->assertEquals($enabled, $this->inventoryHelper->isNegativeQtyEnabled());
    }

    /**
     * @dataProvider boolDataProvider
     * @param bool $enabled
     * @return void
     */
    public function testIsInventoryAdjustmentEnabled(bool $enabled): void
    {
        $this->mockScopeConfig
            ->method('isSetFlag')
            ->with('orderflow_inventory_import/settings/adjust_inventory')
            ->willReturn($enabled);

        $this->assertEquals($enabled, $this->inventoryHelper->isInventoryAdjustmentEnabled());
    }

    /**
     * @dataProvider quoteAdjustmentEnabledDataProvider
     * @param int $flag
     * @param bool $unsentEnabled
     * @param bool $activeEnabled
     * @return void
     */
    public function testQuoteAdjustmentEnabled(int $flag, bool $unsentEnabled, bool $activeEnabled): void
    {
        $this->mockScopeConfig
            ->method('getValue')
            ->with(
                'orderflow_inventory_import/settings/adjust_inventory',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
            )
            ->willReturn($flag);

        $this->assertEquals($unsentEnabled, $this->inventoryHelper->isUnsentOrderAdjustmentEnabled());
        $this->assertEquals($activeEnabled, $this->inventoryHelper->isActiveQuoteAdjustmentEnabled());
    }

    public function testGetValidUnsentOrderStatuses(): void
    {
        $this->mockScopeConfig
            ->method('getValue')
            ->with(
                'orderflow_inventory_import/settings/valid_unsent_order_statuses',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
            )
            ->willReturn('processing,complete');

        $this->assertEquals(['processing', 'complete'], $this->inventoryHelper->getValidUnsentOrderStatuses());
    }

    public function testGetActiveQuoteCutoff(): void
    {
        $cutoff = rand(1, 100);

        $this->mockScopeConfig
            ->method('getValue')
            ->with(
                'orderflow_inventory_import/settings/active_quote_cutoff',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
            )
            ->willReturn($cutoff);

        $this->assertEquals($cutoff, $this->inventoryHelper->getActiveQuoteCutoff());
    }

    public function testGetActiveQuoteCutoffDate(): void
    {
        $cutoff = rand(1, 100);

        $this->mockScopeConfig
            ->method('getValue')
            ->with(
                'orderflow_inventory_import/settings/active_quote_cutoff',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
            )
            ->willReturn($cutoff);

        $expectedDate = date('Y-m-d H:i:s', strtotime('-' . $cutoff . ' days'));

        $this->assertEquals(
            $expectedDate,
            $this->inventoryHelper->getActiveQuoteCutoffDate()
        );
    }

    public function testGetUnsentOrderCutoff(): void
    {
        $cutoff = rand(1, 100);

        $this->mockScopeConfig
            ->method('getValue')
            ->with(
                'orderflow_inventory_import/settings/unsent_order_cutoff',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
            )
            ->willReturn($cutoff);

        $this->assertEquals($cutoff, $this->inventoryHelper->getUnsentOrderCutoff());
    }

    public function testGetUnsentOrderCutoffDate(): void
    {
        $cutoff = rand(1, 100);

        $this->mockScopeConfig
            ->method('getValue')
            ->with(
                'orderflow_inventory_import/settings/unsent_order_cutoff',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
            )
            ->willReturn($cutoff);

        $expectedDate = date('Y-m-d H:i:s', strtotime('-' . $cutoff . ' days'));

        $this->assertEquals(
            $expectedDate,
            $this->inventoryHelper->getUnsentOrderCutoffDate()
        );
    }

    public function quoteAdjustmentEnabledDataProvider(): array
    {
        return [
            [2, true, true],
            [1, true, false],
            [0, false, false],
        ];
    }

    public function boolDataProvider(): array
    {
        return [
            [true],
            [false],
        ];
    }
}