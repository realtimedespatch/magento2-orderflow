<?php

namespace RealtimeDespatch\OrderFlow\Model;

use RealtimeDespatch\OrderFlow\Api\Data\ImportLineInterface;
use Magento\Framework\DataObject\IdentityInterface;

class ImportLine extends \Magento\Framework\Model\AbstractModel implements ImportLineInterface, IdentityInterface
{
    /**
     * @inheritdoc
     */
    const CACHE_TAG = 'rtd_import_line';

    /**
     * @inheritdoc
     */
    protected $_cacheTag = 'rtd_import_line';

    /**
     * @inheritdoc
     */
    protected $_eventPrefix = 'rtd_import_line';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('RealtimeDespatch\OrderFlow\Model\ResourceModel\ImportLine');
    }

    /**
     * @inheritdoc
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
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
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ImportLineInterface
     */
    public function setImportId($importId)
    {
        return $this->setData(self::IMPORT_ID, $importId);
    }

    /**
     * Set Sequence Id
     *
     * @param integer $sequenceId
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ImportLineInterface
     */
    public function setSequenceId($sequenceId)
    {
        return $this->setData(self::SEQUENCE_ID, $sequenceId);
    }

    /**
     * Set Result
     *
     * @param string $result
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ImportLineInterface
     */
    public function setResult($result)
    {
        return $this->setData(self::RESULT, $result);
    }

    /**
     * Set Reference
     *
     * @param string $reference
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ImportLineInterface
     */
    public function setReference($reference)
    {
        return $this->setData(self::REFERENCE, $reference);
    }

    /**
     * Set Operation
     *
     * @param string $operation
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ImportLineInterface
     */
    public function setOperation($operation)
    {
        return $this->setData(self::OPERATION, $operation);
    }

    /**
     * Set Entity
     *
     * @param string $entity
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ImportLineInterface
     */
    public function setEntity($entity)
    {
        return $this->setData(self::ENTITY, $entity);
    }

    /**
     * Set Message
     *
     * @param string $message
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ImportLineInterface
     */
    public function setMessage($message)
    {
        return $this->setData(self::MESSAGE, $message);
    }

    /**
     * Set Additional Data
     *
     * @param string $additionalData
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ImportLineInterface
     */
    public function setAdditionalData($additionalData)
    {
        return $this->setData(self::ADDITIONAL_DATA, $additionalData);
    }

    /**
     * Set Created Timestamp
     *
     * @param string $created
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ImportLineInterface
     */
    public function setCreatedAt($created)
    {
        return $this->setData(self::CREATED_AT, $created);
    }

    /**
     * Set Processed Timestamp
     *
     * @param string $processed
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ImportLineInterface
     */
    public function setProcessedAt($processed)
    {
        return $this->setData(self::PROCESSED_AT, $processed);
    }
}