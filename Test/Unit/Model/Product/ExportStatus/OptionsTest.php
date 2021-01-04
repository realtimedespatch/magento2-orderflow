<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\Product\ExportStatus;

use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Model\Product\ExportStatus\Options;

class OptionsTest extends TestCase
{
    /**
     * @var Options
     */
    protected $options;

    public function setUp()
    {
        $this->options = new Options();
    }

    public function testToOptionArray()
    {
        $expectedValue = [
            ['value' => Options::STATUS_PENDING, 'label' => Options::STATUS_PENDING],
            ['value' => Options::STATUS_QUEUED, 'label' => Options::STATUS_QUEUED],
            ['value' => Options::STATUS_EXPORTED, 'label' => Options::STATUS_EXPORTED],
            ['value' => Options::STATUS_FAILED, 'label' => Options::STATUS_FAILED]
        ];

        $this->assertEquals($expectedValue, $this->options->toOptionArray());
    }
}
