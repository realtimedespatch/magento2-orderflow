<?php

namespace RealtimeDespatch\OrderFlow\Model;

use RealtimeDespatch\OrderFlow\Api\Data\ExportInterface;

class Export extends \Magento\Framework\Model\AbstractModel implements ExportInterface
{
    /**
     * @inheritdoc
     */
    protected $_eventPrefix = 'rtd_export';

    /**
     * Export Lines.
     *
     * @var array
     */
    protected $_lines;

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('RealtimeDespatch\OrderFlow\Model\ResourceModel\Export');
        $this->_lines = array();
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::EXPORT_ID);
    }

    /**
     * Get Request ID
     *
     * @return int|null
     */
    public function getRequestId()
    {
        return $this->getData(self::REQUEST_ID);
    }

    /**
     * Get Export Lines
     *
     * @return array
     */
    public function getLines()
    {
        return $this->_lines;
    }

    /**
     * Get Message ID
     *
     * @return int|null
     */
    public function getMessageId()
    {
        return $this->getData(self::MESSAGE_ID);
    }

    /**
     * Get Scope ID
     *
     * @return int|null
     */
    public function getScopeId()
    {
        return $this->getData(self::SCOPE_ID);
    }

    /**
     * Get Entity
     *
     * @return string|null
     */
    public function getEntity()
    {
        return $this->getData(self::ENTITY);
    }

    /**
     * Get Operation
     *
     * @return string|null
     */
    public function getOperation()
    {
        return $this->getData(self::OPERATION);
    }

    /**
     * Get Successes
     *
     * @return int|null
     */
    public function getSuccesses()
    {
        return $this->getData(self::SUCCESSES);
    }

    /**
     * Get Duplicates
     *
     * @return int|null
     */
    public function getDuplicates()
    {
        return $this->getData(self::DUPLICATES);
    }

    /**
     * Get Superseded
     *
     * @return int|null
     */
    public function getSuperseded()
    {
        return $this->getData(self::SUPERSEDED);
    }

    /**
     * Get Failures
     *
     * @return int|null
     */
    public function getFailures()
    {
        return $this->getData(self::FAILURES);
    }

    /**
     * Get Created Timestamp
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Get Viewed Timestamp
     *
     * @return string|null
     */
    public function getViewedAt()
    {
        return $this->getData(self::VIEWED_AT);
    }

    /**
     * Set Request Id
     *
     * @param integer $requestId
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportInterface
     */
    public function setRequestId($requestId)
    {
        return $this->setData(self::REQUEST_ID, $requestId);
    }

    /**
     * Adds a line to the export.
     *
     * @param \RealtimeDespatch\OrderFlow\Api\Data\ExportLineInterface $line
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportInterface
     */
    public function addLine(\RealtimeDespatch\OrderFlow\Api\Data\ExportLineInterface $line)
    {
        $line->setExport($this);
        $this->_lines[] = $line;
    }

    /**
     * Set Export Lines.
     *
     * @param array $lines
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportInterface
     */
    public function setLines($lines)
    {
        $this->_lines = $lines;

        return $this;
    }

    /**
     * Set Message Id
     *
     * @param integer $messageId
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportInterface
     */
    public function setMessageId($messageId)
    {
        return $this->setData(self::MESSAGE_ID, $messageId);
    }

    /**
     * Set Scope Id
     *
     * @param integer $scopeId
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportInterface
     */
    public function setScopeId($scopeId)
    {
        return $this->setData(self::SCOPE_ID, $scopeId);
    }

    /**
     * Set Entity
     *
     * @param string $entity
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportInterface
     */
    public function setEntity($entity)
    {
        return $this->setData(self::ENTITY, $entity);
    }

    /**
     * Set operation
     *
     * @param string $operation
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportInterface
     */
    public function setOperation($operation)
    {
        return $this->setData(self::OPERATION, $operation);
    }

    /**
     * Set Successes
     *
     * @param integer $successes
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportInterface
     */
    public function setSuccesses($successes)
    {
        return $this->setData(self::SUCCESSES, $successes);
    }

    /**
     * Set Duplicates
     *
     * @param integer $duplicates
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportInterface
     */
    public function setDuplicates($duplicates)
    {
        return $this->setData(self::DUPLICATES, $duplicates);
    }

    /**
     * Set Superseded
     *
     * @param integer $superseded
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportInterface
     */
    public function setSuperseded($superseded)
    {
        return $this->setData(self::SUPERSEDED, $superseded);
    }

    /**
     * Set Failures
     *
     * @param integer $failures
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportInterface
     */
    public function setFailures($failures)
    {
        return $this->setData(self::FAILURES, $failures);
    }

    /**
     * Set Created Timestamp
     *
     * @param string $created
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportInterface
     */
    public function setCreatedAt($created)
    {
        return $this->setData(self::CREATED_AT, $created);
    }

    /**
     * Set Viewed Timestamp
     *
     * @param string $viewed
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportInterface
     */
    public function setViewedAt($viewed)
    {
        return $this->setData(self::VIEWED_AT, $viewed);
    }

    /**
     * Checks whether this is a product export.
     *
     * @return boolean
     */
    public function isProductExport()
    {
        return $this->getEntity() === self::ENTITY_PRODUCT;
    }

    /**
     * Checks whether this is an order export.
     *
     * @return boolean
     */
    public function isOrderExport()
    {
        return $this->getEntity() === self::ENTITY_ORDER;
    }
}