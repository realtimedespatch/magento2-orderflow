<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\Product\ExportStatus;

use RealtimeDespatch\OrderFlow\Model\Product\ExportStatus\Options;

class OptionsTest extends \PHPUnit\Framework\TestCase
{
    protected Options $options;

    protected function setUp(): void
    {
        $this->options = new Options();
    }

    public function testToOptionArray()
    {
        $result = $this->options->toOptionArray();
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $expected = array_map(fn($x) => ['value' => $x, 'label' => $x], ['Pending', 'Queued', 'Exported', 'Failed']);
        $this->assertEquals($expected, $result);
    }
}