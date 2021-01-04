<?php

namespace RealtimeDespatch\OrderFlow\Model\Product\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Framework\Phrase;

class ExportStatus extends AbstractSource
{
    const STATUS_PENDING = 'Pending';
    const STATUS_QUEUED = 'Queued';
    const STATUS_EXPORTED = 'Exported';
    const STATUS_FAILED = 'Failed';

    /**
     * @return array
     */
    public function getAllOptions()
    {
        return [
            ['value' => self::STATUS_PENDING, 'label' => $this->getLabel(self::STATUS_PENDING)],
            ['value' => self::STATUS_QUEUED, 'label' => $this->getLabel(self::STATUS_QUEUED)],
            ['value' => self::STATUS_EXPORTED, 'label' => $this->getLabel(self::STATUS_EXPORTED)],
            ['value' => self::STATUS_FAILED, 'label' => $this->getLabel(self::STATUS_FAILED)]
        ];
    }

    /**
     * Label Getter.
     *
     * @param $label
     * @return Phrase
     */
    protected function getLabel($label)
    {
        return __($label);
    }
}
