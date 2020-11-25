<?php

namespace RealtimeDespatch\OrderFlow\Model\ResourceModel\Import;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use RealtimeDespatch\OrderFlow\Model\Import;

class Collection extends AbstractCollection
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
        $this->_init(
            Import::class,
            \RealtimeDespatch\OrderFlow\Model\ResourceModel\Import::class
        );
    }
}
