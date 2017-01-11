<?php

namespace RealtimeDespatch\OrderFlow\Model\Source\Export;

/**
 * Class Status
 * @package RealtimeDespatch\OrderFlow\Model\Source\Export
 * @codeCoverageIgnore
 */
class Status implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'Pending', 'label' => __('Pending')],
            ['value' => 'Queued', 'label' => __('Queued')],
            ['value' => 'Exported', 'label' => __('Exported')],
            ['value' => 'Failed', 'label' => __('Failed')]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return ['Pending' => __('Pending'), 'Queued' => __('Queued'), 'Exported' => __('Exported'), 'Failed' => __('Failed')];
    }
}