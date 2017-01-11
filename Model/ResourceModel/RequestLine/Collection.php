<?php

namespace RealtimeDespatch\OrderFlow\Model\ResourceModel\RequestLine;

/**
 * Class Collection
 * @package RealtimeDespatch\OrderFlow\Model\ResourceModel\RequestLine
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected $_idFieldName = 'line_id';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('RealtimeDespatch\OrderFlow\Model\RequestLine', 'RealtimeDespatch\OrderFlow\Model\ResourceModel\RequestLine');
    }
}