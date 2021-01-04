<?php

namespace RealtimeDespatch\OrderFlow\Model\Source\Inventory;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Phrase;

/**
 * Inventory Adjustment Source Options.
 */
class Adjustment implements OptionSourceInterface
{
    const ADJUSTMENT_NO = 'No';
    const ADJUSTMENT_UNSENT = 'Unsent Orders';
    const ADJUSTMENT_UNSENT_AND_ACTIVE = 'Unsent Orders and Active Quotes';

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => $this->getLabel(self::ADJUSTMENT_NO)],
            ['value' => 1, 'label' => $this->getLabel(self::ADJUSTMENT_UNSENT)],
            ['value' => 2, 'label' => $this->getLabel(self::ADJUSTMENT_UNSENT_AND_ACTIVE)]
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
            0 => $this->getLabel(self::ADJUSTMENT_NO),
            1 => $this->getLabel(self::ADJUSTMENT_UNSENT),
            2 => $this->getLabel(self::ADJUSTMENT_UNSENT_AND_ACTIVE)
        ];
    }

    /**
     * Label Getter.
     *
     * Required as constants should not be used as arguments to the translate function.
     *
     * @param $status
     * @return Phrase
     */
    protected function getLabel($status)
    {
        return __($status);
    }
}
