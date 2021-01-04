<?php

namespace RealtimeDespatch\OrderFlow\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Request Resource Model
 */
class Request extends AbstractDb
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
        $this->_init('rtd_requests', 'request_id');
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

        /* @var \RealtimeDespatch\OrderFlow\Model\Request $object */
        if ($object->isObjectNew() && ! $object->getMessageId()) {
            $messageId = md5($object->getType().$object->getEntity().uniqid()); // phpcs:ignore
            $object->setMessageId($messageId);
        }

        return parent::_beforeSave($object);
    }
}
