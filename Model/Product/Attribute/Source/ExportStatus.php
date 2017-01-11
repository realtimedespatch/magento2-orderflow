<?php

namespace RealtimeDespatch\OrderFlow\Model\Product\Attribute\Source;

/**
 * Class ExportStatus
 * @package RealtimeDespatch\OrderFlow\Model\Product\Attribute\Source
 * @codeCoverageIgnore
 */
class ExportStatus extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
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