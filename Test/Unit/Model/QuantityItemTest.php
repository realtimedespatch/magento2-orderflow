<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Model\QuantityItem;

class QuantityItemTest extends TestCase
{
    protected $quantityItem;

    public function setUp()
    {
        $this->quantityItem = new QuantityItem();
    }

    public function testGetAndSetSku()
    {
        $sku = 'TESTSKU';

        $this->assertNull($this->quantityItem->getSku());
        $this->quantityItem->setSku($sku);
        $this->assertEquals($sku, $this->quantityItem->getSku());
    }

    public function testGetAndSetQty()
    {
        $qty = 666;

        $this->assertNull($this->quantityItem->getQty());
        $this->quantityItem->setQty($qty);
        $this->assertEquals($qty, $this->quantityItem->getQty());
    }
}
