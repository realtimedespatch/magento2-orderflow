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

    /**
     * Importable Requests Getter.
     *
     * @param string $entityType
     * @param int $batchLimit
     * @return Collection
     */
    public function getImportableRequests(string $entityType, int $batchLimit)
    {
        return $this
            ->addFieldToFilter('type', ['eq' => 'Import'])
            ->addFieldToFilter('entity', ['eq' => $entityType])
            ->addFieldToFilter('processed_at', ['null' => true])
            ->setOrder('message_id', 'ASC')
            ->setPageSize($batchLimit)
            ->setCurPage(1);
    }

    /**
     * Exportable Requests Getter.
     *
     * @param string $entityType
     * @param int $scopeId
     * @param int $batchLimit
     * @return Collection
     */
    public function getExportableRequests(string $entityType, int $scopeId, int $batchLimit)
    {
        return $this
            ->addFieldToFilter('type', ['eq' => 'Export'])
            ->addFieldToFilter('entity', ['eq' => $entityType])
            ->addFieldToFilter('scope_id', ['eq' => $scopeId])
            ->addFieldToFilter('processed_at', ['null' => true])
            ->setOrder('message_id', 'ASC')
            ->setPageSize($batchLimit)
            ->setCurPage(1);
    }

    /**
     * Deletes requests older than a designated cutoff.
     *
     * @param string $cutoff
     */
    public function deleteOlderThanCutoff(string $cutoff)
    {
        $this->addFieldToFilter('created_at', ['lteq' => $cutoff])->walk('delete');
    }
}
