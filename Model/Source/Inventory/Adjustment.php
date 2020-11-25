<?php

namespace RealtimeDespatch\OrderFlow\Model\Source\Inventory;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Inventory Adjustment Source Options.
 */
class Adjustment implements OptionSourceInterface
{
    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('No')],
            ['value' => 1, 'label' => __('Unsent Orders')],
            ['value' => 2, 'label' => __('Unsent Orders and Active Quotes')]
        ];
    }

    /**
     * Return Options as Array.
     *
     * @return array
     */
    public function toArray()
    {
        return [0 => __('No'), 1 => __('Unsent Orders'), 2 => __('Unsent Orders and Active Quotes')];
    }
}
