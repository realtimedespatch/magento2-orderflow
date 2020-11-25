<?php

namespace RealtimeDespatch\OrderFlow\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Export Resource Model
 */
class Export extends AbstractDb
{
    /**
     * @var DateTime
     */
    protected $date;

    /**
     * Construct
     *
     * @param Context $context
     * @param DateTime $date
     * @param string|null $resourcePrefix
     */
    public function __construct(
        Context $context,
        DateTime $date,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->date = $date;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('rtd_exports', 'export_id');
    }

    /**
     * Get export identifier by request_id
     *
     * @param string $requestId
     * @return int|false
     */
    public function getIdByRequestId(string $requestId)
    {
        $connection = $this->getConnection();

        $select = $connection
            ->select()
            ->from($this->getConnection()
            ->getTableName('rtd_exports'), 'export_id')
            ->where('request_id = :request_id');

        $bind = [':request_id' => (integer)$requestId];

        return $connection->fetchOne($select, $bind);
    }

    /**
     * Process post data before saving
     *
     * @param AbstractModel $object
     * @return $this
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if ($object->isObjectNew() && ! $object->getData('creation_time')) {
            $object->setData('creation_time', $this->date->gmtDate());
        }

        return parent::_beforeSave($object);
    }
}
