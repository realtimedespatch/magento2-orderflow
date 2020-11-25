<?php

namespace RealtimeDespatch\OrderFlow\Model\ResourceModel\ExportLine;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use RealtimeDespatch\OrderFlow\Model\ExportLine;

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
            ExportLine::class,
            \RealtimeDespatch\OrderFlow\Model\ResourceModel\ExportLine::class
        );
    }
}
