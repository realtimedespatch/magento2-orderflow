<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\Config\Source;

use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Model\Config\Source\Duration;

class DurationTest extends TestCase
{
    /**
     * @var Duration
     */
    protected $duration;

    protected function setUp()
    {
        $this->duration = new Duration();
    }

    public function testToOptionArray()
    {
        $expectedResult = [
            1 => __('1 Day'),
            5 => __('5 Days'),
            10 => __('10 Days'),
            15 => __('15 Days'),
            20 => __('20 Days'),
            25 => __('25 Days'),
            30 => __('30 Days')
        ];

        $this->assertEquals($expectedResult, $this->duration->toOptionArray());
    }
}
