<?php

namespace RealtimeDespatch\OrderFlow\Model\Source\Inventory;

/**
 * Class Adjustment
 * @package RealtimeDespatch\OrderFlow\Model\Source\Inventory
 * @codeCoverageIgnore
 */
class Adjustment implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [0 => __('No'), 1 => __('Unsent Orders'), 2 => __('Unsent Orders and Active Quotes')];
    }
}