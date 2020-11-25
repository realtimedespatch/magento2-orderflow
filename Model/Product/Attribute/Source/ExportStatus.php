<?php

namespace RealtimeDespatch\OrderFlow\Model\Product\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class ExportStatus extends AbstractSource
{
    /**
     * @return array
     */
    public function getAllOptions()
    {
        return [
            ['value' => 'Pending', 'label' => __('Pending')],
            ['value' => 'Queued', 'label' => __('Queued')],
            ['value' => 'Exported', 'label' => __('Exported')],
            ['value' => 'Failed', 'label' => __('Failed')]
        ];
    }
}
