<?php

namespace RealtimeDespatch\OrderFlow\Model\ResourceModel\RequestLine;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use RealtimeDespatch\OrderFlow\Model\RequestLine;

class Collection extends AbstractCollection
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
        $this->_init(
            RequestLine::class,
            \RealtimeDespatch\OrderFlow\Model\ResourceModel\RequestLine::class
        );
    }
}
