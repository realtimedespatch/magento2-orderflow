<?php

namespace RealtimeDespatch\OrderFlow\Model;

use Magento\Framework\Model\AbstractModel;
use RealtimeDespatch\OrderFlow\Api\Data\ExportLineInterface;

class ExportLine extends AbstractModel implements ExportLineInterface
{
    /**
     * @inheritdoc
     */
    protected $_eventPrefix = 'rtd_export_line';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\ExportLine::class);
    }

    /**
     * Get ID
     *
     * @return integer|null
     */
    public function getId()
    {
        return $this->getData(self::LINE_ID);
    }

    /**
     * Get Export ID
     *
     * @return integer|null
     */
    public function getExportId()
    {
        return $this->getData(self::IMPORT_ID);
    }

    /**
     * Get Result
     *
     * @return string|null
     */
    public function getResult()
    {
        return $this->getData(self::RESULT);
    }

    /**
     * Get Reference
     *
     * @return string|null
     */
    public function getReference()
    {
        return $this->getData(self::REFERENCE);
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
     * Get Entity
     *
     * @return string|null
     */
    public function getEntity()
    {
        return $this->getData(self::ENTITY);
    }

    /**
     * Get Message
     *
     * @return string|null
     */
    public function getMessage()
    {
        return $this->getData(self::MESSAGE);
    }

    /**
     * Get Detail
     *
     * @return string|null
     */
    public function getDetail()
    {
        return $this->getData(self::DETAIL);
    }

    /**
     * Get Additional Data
     *
     * @return string|null
     */
    public function getAdditionalData()
    {
        return $this->getData(self::ADDITIONAL_DATA);
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
     * Get Processed Timestamp
     *
     * @return string|null
     */
    public function getProcessedAt()
    {
        return $this->getData(self::PROCESSED_AT);
    }

    /**
     * Set Export Id
     *
     * @param integer $exportId
     *
     * @return ExportLineInterface
     */
    public function setExportId(int $exportId)
    {
        return $this->setData(self::IMPORT_ID, $exportId);
    }

    /**
     * Set Result
     *
     * @param string $result
     *
     * @return ExportLineInterface
     */
    public function setResult(string $result)
    {
        return $this->setData(self::RESULT, $result);
    }

    /**
     * Set Reference
     *
     * @param string $reference
     *
     * @return ExportLineInterface
     */
    public function setReference(string $reference)
    {
        return $this->setData(self::REFERENCE, $reference);
    }

    /**
     * Set Operation
     *
     * @param string $operation
     *
     * @return ExportLineInterface
     */
    public function setOperation(string $operation)
    {
        return $this->setData(self::OPERATION, $operation);
    }

    /**
     * Set Entity
     *
     * @param string $entity
     *
     * @return ExportLineInterface
     */
    public function setEntity(string $entity)
    {
        return $this->setData(self::ENTITY, $entity);
    }

    /**
     * Set Message
     *
     * @param string $message
     *
     * @return ExportLineInterface
     */
    public function setMessage(string $message)
    {
        return $this->setData(self::MESSAGE, $message);
    }

    /**
     * Set Detail
     *
     * @param string $detail
     *
     * @return ExportLineInterface
     */
    public function setDetail(string $detail)
    {
        return $this->setData(self::DETAIL, $detail);
    }

    /**
     * Set Additional Data
     *
     * @param string $additionalData
     *
     * @return ExportLineInterface
     */
    public function setAdditionalData(string $additionalData)
    {
        return $this->setData(self::ADDITIONAL_DATA, $additionalData);
    }

    /**
     * Set Created Timestamp
     *
     * @param string $created
     *
     * @return ExportLineInterface
     */
    public function setCreatedAt(string $created)
    {
        return $this->setData(self::CREATED_AT, $created);
    }

    /**
     * Set Processed Timestamp
     *
     * @param string $processed
     *
     * @return ExportLineInterface
     */
    public function setProcessedAt(string $processed)
    {
        return $this->setData(self::PROCESSED_AT, $processed);
    }

    /**
     * Checks whether this is an export operation.
     *
     * @return boolean
     */
    public function isExport()
    {
        return $this->getOperation() === self::OP_EXPORT;
    }

    /**
     * Checks whether this is a cancellation operation.
     *
     * @return boolean
     */
    public function isCancellation()
    {
        return $this->getOperation() === self::OP_CANCEL;
    }

    /**
     * Checks whether this export line was successful
     *
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->getResult() === self::RESULT_SUCCESS;
    }

    /**
     * Checks whether this export line was a failure
     *
     * @return boolean
     */
    public function isFailure()
    {
        return $this->getResult() === self::RESULT_FAILURE;
    }

    /**
     * Returns the export status for the related entity.
     *
     * @return mixed
     */
    public function getEntityExportStatus()
    {
        if ($this->isFailure()) {
            return self::ENTITY_STATUS_FAILED;
        }

        if ($this->isExport()) {
            return self::ENTITY_STATUS_EXPORTED;
        }

        if ($this->isCancellation() && $this->isSuccess()) {
            return self::ENTITY_STATUS_CANCELLED;
        }

        return self::ENTITY_STATUS_QUEUED;
    }
}
