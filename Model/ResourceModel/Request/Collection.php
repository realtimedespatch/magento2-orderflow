<?php

namespace RealtimeDespatch\OrderFlow\Model\ResourceModel\Request;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use RealtimeDespatch\OrderFlow\Model\Request;

class Collection extends AbstractCollection
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
        $this->_init(
            Request::class,
            \RealtimeDespatch\OrderFlow\Model\ResourceModel\Request::class
        );
    }
}
