<?php

namespace RealtimeDespatch\OrderFlow\Model\ResourceModel;

/**
 * Import Resource Model
 */
class Import extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param string|null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        $resourcePrefix = null
    )
    {
        parent::__construct($context, $resourcePrefix);
        $this->_date = $date;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('rtd_imports', 'import_id');
    }

    /**
     * Get export identifier by request_id
     *
     * @param string $requestId
     * @return int|false
     */
    public function getIdByRequestId($requestId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from($this->getConnection()->getTableName('rtd_imports'), 'import_id')->where('request_id = :request_id');

        $bind = [':request_id' => (integer)$requestId];

        return $connection->fetchOne($select, $bind);
    }

    /**
     * Process post data before saving
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->isObjectNew() && ! $object->hasCreationTime()) {
            $object->setCreationTime($this->_date->gmtDate());
        }

        return parent::_beforeSave($object);
    }
}