<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\Source\Inventory;

use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Model\Source\Inventory\Adjustment;

class AdjustmentTest extends TestCase
{
    /**
     * @var Adjustment
     */
    protected $adjustment;

    public function setUp()
    {
        $this->adjustment = new Adjustment();
    }

    public function testToOptionArray()
    {
        $expectedValue = [
            ['value' => 0, 'label' => __(Adjustment::ADJUSTMENT_NO)],
            ['value' => 1, 'label' => __(Adjustment::ADJUSTMENT_UNSENT)],
            ['value' => 2, 'label' => __(Adjustment::ADJUSTMENT_UNSENT_AND_ACTIVE)]
        ];

        $this->assertEquals($expectedValue, $this->adjustment->toOptionArray());
    }

    public function testToArray()
    {
        $expectedValue = [
            0 => __(Adjustment::ADJUSTMENT_NO),
            1 => __(Adjustment::ADJUSTMENT_UNSENT),
            2 => __(Adjustment::ADJUSTMENT_UNSENT_AND_ACTIVE)
        ];

        $this->assertEquals($expectedValue, $this->adjustment->toArray());
    }
}
