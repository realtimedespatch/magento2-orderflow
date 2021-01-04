<?php

namespace RealtimeDespatch\OrderFlow\Model\Source\Export;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Phrase;

/**
 * Export Status Source Options.
 */
class Status implements OptionSourceInterface
{
    const STATUS_PENDING = 'Pending';
    const STATUS_QUEUED = 'Queued';
    const STATUS_EXPORTED = 'Exported';
    const STATUS_FAILED = 'Failed';
    const STATUS_CANCELLED = 'Cancelled';

    /**
     * @inheritdoc
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::STATUS_PENDING, 'label' => $this->getLabel(self::STATUS_PENDING)],
            ['value' => self::STATUS_QUEUED, 'label' => $this->getLabel(self::STATUS_QUEUED)],
            ['value' => self::STATUS_EXPORTED, 'label' => $this->getLabel(self::STATUS_EXPORTED)],
            ['value' => self::STATUS_FAILED, 'label' => $this->getLabel(self::STATUS_FAILED)],
            ['value' => self::STATUS_CANCELLED, 'label' => $this->getLabel(self::STATUS_CANCELLED)],
        ];
    }

    /**
     * Return Options as Array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            self::STATUS_PENDING => $this->getLabel(self::STATUS_PENDING),
            self::STATUS_QUEUED => $this->getLabel(self::STATUS_QUEUED),
            self::STATUS_EXPORTED => $this->getLabel(self::STATUS_EXPORTED),
            self::STATUS_FAILED => $this->getLabel(self::STATUS_FAILED),
            self::STATUS_CANCELLED => $this->getLabel(self::STATUS_CANCELLED),
        ];
    }

    /**
     * Label Getter.
     *
     * Required as constants should not be used as arguments to the translate function.
     *
     * @param $label
     * @return Phrase
     */
    protected function getLabel($label)
    {
        return __($label);
    }
}
