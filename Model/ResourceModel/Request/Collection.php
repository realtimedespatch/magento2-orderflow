<?php

namespace RealtimeDespatch\OrderFlow\Model\ResourceModel\Request;

/**
 * Class Collection
 * @package RealtimeDespatch\OrderFlow\Model\ResourceModel\Request
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected $_idFieldName = 'request_id';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('RealtimeDespatch\OrderFlow\Model\Request', 'RealtimeDespatch\OrderFlow\Model\ResourceModel\Request');
    }
}