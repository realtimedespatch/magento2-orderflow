<?php

namespace RealtimeDespatch\OrderFlow\Model\Source\Export;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Export Status Source Options.
 */
class Status implements OptionSourceInterface
{
    /**
     * @inheritdoc
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
     * Return Options as Array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'Pending' => __('Pending'),
            'Queued' => __('Queued'),
            'Exported' => __('Exported'),
            'Failed' => __('Failed')
        ];
    }
}
