<?php

namespace RealtimeDespatch\OrderFlow\Model\ResourceModel\ImportLine;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use RealtimeDespatch\OrderFlow\Model\ImportLine;

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
            ImportLine::class,
            \RealtimeDespatch\OrderFlow\Model\ResourceModel\ImportLine::class
        );
    }
}
