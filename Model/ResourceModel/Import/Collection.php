<?php

namespace RealtimeDespatch\OrderFlow\Model\ResourceModel\Import;

use Magento\Framework\DataObject;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use RealtimeDespatch\OrderFlow\Api\Data\ImportInterface;
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

    /**
     * Deletes imports older than a designated cutoff.
     *
     * @param string $cutoff
     */
    public function deleteOlderThanCutoff(string $cutoff)
    {
        $this->addFieldToFilter('created_at', ['lteq' => $cutoff])->walk('delete');
    }

    /**
     * Unread Failed Import Getter.
     *
     * @return DataObject|ImportInterface
     */
    public function getUnreadFailedImport()
    {
        return $this
            ->addFieldToFilter('failures', ['gt' => 0])
            ->addFieldToFilter('viewed_at', ['null' => true])
            ->setOrder('created_at')
            ->setPageSize(1)
            ->setCurPage(1)
            ->getFirstItem();
    }
}
