<?php

namespace RealtimeDespatch\OrderFlow\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Phrase;

class OperationSource implements OptionSourceInterface
{
    const OPERATION_CREATE = 'Create';
    const OPERATION_UPDATE = 'Update';
    const OPERATION_EXPORT = 'Export';
    const OPERATION_QUEUE = 'Queue';

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::OPERATION_CREATE, 'label' => $this->getLabel(self::OPERATION_CREATE)],
            ['value' => self::OPERATION_UPDATE, 'label' => $this->getLabel(self::OPERATION_UPDATE)],
            ['value' => self::OPERATION_EXPORT, 'label' => $this->getLabel(self::OPERATION_EXPORT)],
            ['value' => self::OPERATION_QUEUE, 'label' => $this->getLabel(self::OPERATION_QUEUE)]
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
            self::OPERATION_CREATE => $this->getLabel(self::OPERATION_CREATE),
            self::OPERATION_UPDATE => $this->getLabel(self::OPERATION_UPDATE),
            self::OPERATION_EXPORT => $this->getLabel(self::OPERATION_EXPORT),
            self::OPERATION_QUEUE => $this->getLabel(self::OPERATION_QUEUE),
        ];
    }

    /**
     * Label Getter.
     *
     * @param $operation
     * @return Phrase
     */
    protected function getLabel($operation)
    {
        return __($operation);
    }
}
