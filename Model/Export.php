<?php

namespace RealtimeDespatch\OrderFlow\Model;

use Magento\Framework\Model\AbstractModel;
use RealtimeDespatch\OrderFlow\Api\Data\ExportInterface;
use RealtimeDespatch\OrderFlow\Api\Data\ExportLineInterface;

class Export extends AbstractModel implements ExportInterface
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
    protected $lines;

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Export::class);
        $this->lines = [];
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
        return $this->lines;
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
     * @return ExportInterface
     */
    public function setRequestId(int $requestId)
    {
        return $this->setData(self::REQUEST_ID, $requestId);
    }

    /**
     * Adds a line to the export.
     *
     * @param ExportLineInterface $line
     *
     * @return ExportInterface
     */
    public function addLine(ExportLineInterface $line)
    {
        /* @var ExportLine $line */
        $line->setData('export', $this); // phpcs:ignore
        $this->lines[] = $line;

        return $this;
    }

    /**
     * Set Export Lines.
     *
     * @param mixed $lines
     *
     * @return ExportInterface
     */
    public function setLines($lines)
    {
        $this->lines = $lines;

        return $this;
    }

    /**
     * Set Message Id
     *
     * @param string $messageId
     *
     * @return ExportInterface
     */
    public function setMessageId(string $messageId)
    {
        return $this->setData(self::MESSAGE_ID, $messageId);
    }

    /**
     * Set Scope Id
     *
     * @param integer|null $scopeId
     *
     * @return ExportInterface
     */
    public function setScopeId(int $scopeId = null)
    {
        return $this->setData(self::SCOPE_ID, $scopeId);
    }

    /**
     * Set Entity
     *
     * @param string $entity
     *
     * @return ExportInterface
     */
    public function setEntity(string $entity)
    {
        return $this->setData(self::ENTITY, $entity);
    }

    /**
     * Set operation
     *
     * @param string $operation
     *
     * @return ExportInterface
     */
    public function setOperation(string $operation)
    {
        return $this->setData(self::OPERATION, $operation);
    }

    /**
     * Set Successes
     *
     * @param integer $successes
     *
     * @return ExportInterface
     */
    public function setSuccesses(int $successes)
    {
        return $this->setData(self::SUCCESSES, $successes);
    }

    /**
     * Set Duplicates
     *
     * @param integer $duplicates
     *
     * @return ExportInterface
     */
    public function setDuplicates(int $duplicates)
    {
        return $this->setData(self::DUPLICATES, $duplicates);
    }

    /**
     * Set Superseded
     *
     * @param integer $superseded
     *
     * @return ExportInterface
     */
    public function setSuperseded(int $superseded)
    {
        return $this->setData(self::SUPERSEDED, $superseded);
    }

    /**
     * Set Failures
     *
     * @param integer $failures
     *
     * @return ExportInterface
     */
    public function setFailures(int $failures)
    {
        return $this->setData(self::FAILURES, $failures);
    }

    /**
     * Set Created Timestamp
     *
     * @param string $created
     *
     * @return ExportInterface
     */
    public function setCreatedAt(string $created)
    {
        return $this->setData(self::CREATED_AT, $created);
    }

    /**
     * Set Viewed Timestamp
     *
     * @param string $viewed
     *
     * @return ExportInterface
     */
    public function setViewedAt(string $viewed)
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
