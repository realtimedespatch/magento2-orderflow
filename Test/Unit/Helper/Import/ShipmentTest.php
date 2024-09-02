<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Helper\Import;

use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Helper\Import\Shipment;


class ShipmentTest extends TestCase
{
    protected Shipment $shipmentHelper;
    protected ScopeConfigInterface $mockScopeConfig;

    protected function setUp(): void
    {
        $mockContext = $this->createMock(\Magento\Framework\App\Helper\Context::class);

        $this->mockScopeConfig = $this->createMock(\Magento\Framework\App\Config\ScopeConfigInterface::class);
        $mockContext->method('getScopeConfig')->willReturn($this->mockScopeConfig);

        $this->shipmentHelper = new Shipment(
            $mockContext
        );

        parent::setUp();
    }

    /**
     * @dataProvider isEnabledDataProvider
     */
    public function testIsEnabled($enabled): void
    {
        $this->mockScopeConfig
            ->method('isSetFlag')
            ->with('orderflow_shipment_import/settings/is_enabled')
            ->willReturn($enabled);

        $this->assertEquals($enabled, $this->shipmentHelper->isEnabled());
    }

    public function testGetBatchSize(): void
    {
        $batchSize = rand(1, 100);

        $this->mockScopeConfig
            ->method('getValue')
            ->with('orderflow_shipment_import/settings/batch_size')
            ->willReturn($batchSize);

        $this->assertEquals($batchSize, $this->shipmentHelper->getBatchSize());
    }

    public function isEnabledDataProvider(): array
    {
        return [
            [true],
            [false],
        ];
    }
}