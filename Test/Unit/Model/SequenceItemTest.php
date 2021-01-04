<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Model\SequenceItem;

class SequenceItemTest extends TestCase
{
    protected $sequenceItem;

    public function setUp()
    {
        $this->sequenceItem = new SequenceItem();
    }

    public function testGetAndSetSku()
    {
        $sku = 'TESTSKU';

        $this->assertNull($this->sequenceItem->getSku());
        $this->sequenceItem->setSku($sku);
        $this->assertEquals($sku, $this->sequenceItem->getSku());
    }

    public function testGetAndSetSeq()
    {
        $seq = '123456789';

        $this->assertNull($this->sequenceItem->getSeq());
        $this->sequenceItem->setSeq($seq);
        $this->assertEquals($seq, $this->sequenceItem->getSeq());
    }

    public function testGetLastOrderExported()
    {
        $lastOrderExported = date('Y-m-d H:i:s');

        $this->assertNull($this->sequenceItem->getLastOrderExported());
        $this->sequenceItem->setLastOrderExported($lastOrderExported);
        $this->assertEquals($lastOrderExported, $this->sequenceItem->getLastOrderExported());
    }
}
