<?php

namespace RealtimeDespatch\OrderFlow\Model\Product\ExportStatus;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Export Status Options.
 */
class Options implements OptionSourceInterface
{
    const STATUS_PENDING = 'Pending';
    const STATUS_QUEUED = 'Queued';
    const STATUS_EXPORTED = 'Exported';
    const STATUS_FAILED = 'Failed';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::STATUS_PENDING, 'label' => self::STATUS_PENDING],
            ['value' => self::STATUS_QUEUED, 'label' => self::STATUS_QUEUED],
            ['value' => self::STATUS_EXPORTED, 'label' => self::STATUS_EXPORTED],
            ['value' => self::STATUS_FAILED, 'label' => self::STATUS_FAILED]
        ];
    }
}
