<?php

namespace RealtimeDespatch\OrderFlow\Model\ResourceModel\Export;

use Magento\Framework\DataObject;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use RealtimeDespatch\OrderFlow\Api\Data\ExportInterface;
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

    /**
     * Deletes exports older than a designated cutoff.
     *
     * @param string $cutoff
     */
    public function deleteOlderThanCutoff(string $cutoff)
    {
        $this->addFieldToFilter('created_at', ['lteq' => $cutoff])->walk('delete');
    }

    /**
     * Unread Failed Export Getter.
     *
     * @return DataObject|ExportInterface
     */
    public function getUnreadFailedExport()
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
