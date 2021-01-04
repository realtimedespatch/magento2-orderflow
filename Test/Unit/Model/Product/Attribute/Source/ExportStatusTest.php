<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Model\Product\Attribute\Source;

use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Model\Product\Attribute\Source\ExportStatus;

class ExportStatusTest extends TestCase
{
    /**
     * @var ExportStatus
     */
    protected $exportStatus;

    public function setUp()
    {
        $this->exportStatus = new ExportStatus();
    }

    public function testGetAllOptions()
    {
        $expectedValue = [
            ['value' => ExportStatus::STATUS_PENDING, 'label' => __(ExportStatus::STATUS_PENDING)],
            ['value' => ExportStatus::STATUS_QUEUED, 'label' => __(ExportStatus::STATUS_QUEUED)],
            ['value' => ExportStatus::STATUS_EXPORTED, 'label' => __(ExportStatus::STATUS_EXPORTED)],
            ['value' => ExportStatus::STATUS_FAILED, 'label' => __(ExportStatus::STATUS_FAILED)]
        ];

        $this->assertEquals($expectedValue, $this->exportStatus->getAllOptions());
    }
}
