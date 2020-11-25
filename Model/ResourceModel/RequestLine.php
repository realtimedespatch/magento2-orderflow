<?php

namespace RealtimeDespatch\OrderFlow\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Request Line Resource Model
 */
class RequestLine extends AbstractDb
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
        $this->_init('rtd_request_lines', 'line_id');
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

        /* @var \RealtimeDespatch\OrderFlow\Model\RequestLine $object */
        if ( ! $object->getRequestId() && $object->getRequest()) {
            $object->setRequestId($object->getRequest()->getId());
        }

        return parent::_beforeSave($object);
    }
}
