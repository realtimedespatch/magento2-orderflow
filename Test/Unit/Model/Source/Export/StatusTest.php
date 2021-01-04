<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\Source\Export;

use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Model\Source\Export\Status;

class StatusTest extends TestCase
{
    /**
     * @var Status
     */
    protected $status;

    public function setUp()
    {
        $this->status = new Status();
    }

    public function testToOptionArray()
    {
        $expectedValue = [
            ['value' => Status::STATUS_PENDING, 'label' => __(Status::STATUS_PENDING)],
            ['value' => Status::STATUS_QUEUED, 'label' => __(Status::STATUS_QUEUED)],
            ['value' => Status::STATUS_EXPORTED, 'label' => __(Status::STATUS_EXPORTED)],
            ['value' => Status::STATUS_FAILED, 'label' => __(Status::STATUS_FAILED)],
            ['value' => Status::STATUS_CANCELLED, 'label' => __(Status::STATUS_CANCELLED)],
        ];

        $this->assertEquals($expectedValue, $this->status->toOptionArray());
    }

    public function testToArray()
    {
        $expectedValue = [
            Status::STATUS_PENDING => __(Status::STATUS_PENDING),
            Status::STATUS_QUEUED => __(Status::STATUS_QUEUED),
            Status::STATUS_EXPORTED => __(Status::STATUS_EXPORTED),
            Status::STATUS_FAILED => __(Status::STATUS_FAILED),
            Status::STATUS_CANCELLED => __(Status::STATUS_CANCELLED),
        ];

        $this->assertEquals($expectedValue, $this->status->toArray());
    }
}
