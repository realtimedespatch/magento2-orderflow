<?php

namespace RealtimeDespatch\OrderFlow\Model\ResourceModel\Export;

/**
 * Class Collection
 * @package RealtimeDespatch\OrderFlow\Model\ResourceModel\Export
 * @codeCoverageIgnore
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected $_idFieldName = 'export_id';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('RealtimeDespatch\OrderFlow\Model\Export', 'RealtimeDespatch\OrderFlow\Model\ResourceModel\Export');
    }
}