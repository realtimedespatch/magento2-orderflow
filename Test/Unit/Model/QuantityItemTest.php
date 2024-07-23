<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model;

use RealtimeDespatch\OrderFlow\Model\QuantityItem;

class QuantityItemTest extends AbstractModelTest
{
    protected QuantityItem $quantityItem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->quantityItem = new QuantityItem(
            $this->mockContext,
            $this->mockRegistry,
            $this->mockResource,
            $this->mockResourceCollection
        );
    }

    public function testData(): void
    {
        $this->quantityItem->setSku('SKU-123');
        $this->assertEquals('SKU-123', $this->quantityItem->getSku());

        $qty = rand(1, 100);
        $this->quantityItem->setQty($qty);
        $this->assertEquals($qty, $this->quantityItem->getQty());

        $this->quantityItem->setSource('warehouse');
        $this->assertEquals('warehouse', $this->quantityItem->getSource());
    }
}