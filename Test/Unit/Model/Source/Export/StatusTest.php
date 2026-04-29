<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\Source\Export;

use RealtimeDespatch\OrderFlow\Model\Source\Export\Status;

class StatusTest extends \PHPUnit\Framework\TestCase
{
    public function testToOptionArrayIncludesDisabled(): void
    {
        $source = new Status();

        $result = $source->toOptionArray();

        $this->assertSame(
            ['Pending', 'Queued', 'Exported', 'Failed', 'Disabled'],
            array_map(static fn(array $option) => $option['value'], $result)
        );
    }

    public function testToArrayIncludesDisabled(): void
    {
        $source = new Status();

        $result = $source->toArray();

        $this->assertArrayHasKey('Disabled', $result);
    }
}
