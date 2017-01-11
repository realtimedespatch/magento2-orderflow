<?php

namespace RealtimeDespatch\OrderFlow\Model\Config\Source;

/**
 * Log Duration Source Model.
 *
 * @package RealtimeDespatch\OrderFlow\Model\Config\Source
 * @codeCoverageIgnore
 */
class Duration
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            1 => __('1 Day'),
            5 => __('5 Days'),
            10 => __('10 Days'),
            15 => __('15 Days'),
            20 => __('20 Days'),
            25 => __('25 Days'),
            30 => __('30 Days')
        ];
    }
}