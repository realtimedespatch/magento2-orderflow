<?php

namespace RealtimeDespatch\OrderFlow\Model\ResourceModel\Import;

/**
 * Class Collection
 * @package RealtimeDespatch\OrderFlow\Model\ResourceModel\Import
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected $_idFieldName = 'import_id';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('RealtimeDespatch\OrderFlow\Model\Import', 'RealtimeDespatch\OrderFlow\Model\ResourceModel\Import');
    }
}