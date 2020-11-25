<?php

namespace RealtimeDespatch\OrderFlow\Model\ResourceModel\Export;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use RealtimeDespatch\OrderFlow\Model\Export;

class Collection extends AbstractCollection
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
        $this->_init(
            Export::class,
            \RealtimeDespatch\OrderFlow\Model\ResourceModel\Export::class
        );
    }
}
