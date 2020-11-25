<?php

namespace RealtimeDespatch\OrderFlow\Model\Product\ExportStatus;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Export Status Options.
 */
class Options implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'Pending', 'label' => 'Pending'],
            ['value' => 'Queued', 'label' => 'Queued'],
            ['value' => 'Exported', 'label' => 'Exported'],
            ['value' => 'Failed', 'label' => 'Failed']
        ];
    }
}
