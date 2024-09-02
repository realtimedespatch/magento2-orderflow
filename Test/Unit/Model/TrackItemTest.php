<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model;

use RealtimeDespatch\OrderFlow\Model\TrackItem;

class TrackItemTest extends AbstractModelTest
{
    protected TrackItem $trackItem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->trackItem = new TrackItem(
            $this->mockContext,
            $this->mockRegistry,
            $this->mockResource,
            $this->mockResourceCollection
        );
    }

    public function testData(): void
    {
        $this->trackItem->setTrackingNumber('1234567890');
        $this->assertEquals('1234567890', $this->trackItem->getTrackingNumber());
    }
}