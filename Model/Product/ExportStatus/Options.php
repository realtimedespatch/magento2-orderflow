<?php
/**
 * Created by PhpStorm.
 * User: tbirch
 * Date: 22/03/17
 * Time: 10:50
 */

namespace RealtimeDespatch\OrderFlow\Model\Product\ExportStatus;


class Options implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'Pending', 'label' => 'Pending',
            ),
            array(
                'value' => 'Queued', 'label' => 'Queued',
            ),
            array(
                'value' => 'Exported', 'label' => 'Exported',
            ),
            array(
                'value' => 'Failed', 'label' => 'Failed',
            ),
        );
    }
}