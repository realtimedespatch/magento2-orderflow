<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\Product\Attribute\Source;

use RealtimeDespatch\OrderFlow\Model\Product\Attribute\Source\ExportStatus;

class ExportStatusTest extends \PHPUnit\Framework\TestCase
{
    public function testGetAllOptionsIncludesDisabled(): void
    {
        $source = new ExportStatus();

        $result = $source->getAllOptions();

        $this->assertSame(
            ['Pending', 'Queued', 'Exported', 'Failed', 'Disabled'],
            array_map(static fn(array $option) => $option['value'], $result)
        );
    }
}
