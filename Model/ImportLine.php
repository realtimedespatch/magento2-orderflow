<?php

namespace RealtimeDespatch\OrderFlow\Model;

use Magento\Framework\Model\AbstractModel;
use RealtimeDespatch\OrderFlow\Api\Data\ImportLineInterface;

class ImportLine extends AbstractModel implements ImportLineInterface
{
    /**
     * @inheritdoc
     */
    protected $_eventPrefix = 'rtd_import_line';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\ImportLine::class);
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
     * Get Import ID
     *
     * @return integer|null
     */
    public function getImportId()
    {
        return $this->getData(self::IMPORT_ID);
    }

    /**
     * Get Sequence ID
     *
     * @return integer|null
     */
    public function getSequenceId()
    {
        return $this->getData(self::SEQUENCE_ID);
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
     * Set Import Id
     *
     * @param integer $importId
     *
     * @return ImportLineInterface
     */
    public function setImportId(int $importId)
    {
        return $this->setData(self::IMPORT_ID, $importId);
    }

    /**
     * Set Sequence Id
     *
     * @param string $sequenceId
     *
     * @return ImportLineInterface
     */
    public function setSequenceId(string $sequenceId)
    {
        return $this->setData(self::SEQUENCE_ID, $sequenceId);
    }

    /**
     * Set Result
     *
     * @param string $result
     *
     * @return ImportLineInterface
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
     * @return ImportLineInterface
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
     * @return ImportLineInterface
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
     * @return ImportLineInterface
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
     * @return ImportLineInterface
     */
    public function setMessage(string $message)
    {
        return $this->setData(self::MESSAGE, $message);
    }

    /**
     * Set Additional Data
     *
     * @param string $additionalData
     *
     * @return ImportLineInterface
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
     * @return ImportLineInterface
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
     * @return ImportLineInterface
     */
    public function setProcessedAt(string $processed)
    {
        return $this->setData(self::PROCESSED_AT, $processed);
    }
}
