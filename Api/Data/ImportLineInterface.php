<?php

namespace RealtimeDespatch\OrderFlow\Api\Data;

interface ImportLineInterface
{
    /* Params */
    const LINE_ID         = 'line_id';
    const IMPORT_ID       = 'import_id';
    const SEQUENCE_ID     = 'sequence_id';
    const RESULT          = 'result';
    const REFERENCE       = 'reference';
    const OPERATION       = 'operation';
    const ENTITY          = 'entity';
    const MESSAGE         = 'message';
    const ADDITIONAL_DATA = 'additional_data';
    const CREATED_AT      = 'created_at';
    const PROCESSED_AT    = 'processed_at';

    /* Result Types */
    const RESULT_SUCCESS    = 'Success';
    const RESULT_DUPLICATE  = 'Duplicate';
    const RESULT_FAILURE    = 'Failure';
    const RESULT_SUPERSEDED = 'Superseded';

    /**
     * Get ID
     *
     * @return integer|null
     */
    public function getId();

    /**
     * Get Import ID
     *
     * @return integer|null
     */
    public function getImportId();

    /**
     * Get Sequence ID
     *
     * @return integer|null
     */
    public function getSequenceId();

    /**
     * Get Result
     *
     * @return string|null
     */
    public function getResult();

    /**
     * Get Reference
     *
     * @return string|null
     */
    public function getReference();

    /**
     * Get Operation
     *
     * @return string|null
     */
    public function getOperation();

    /**
     * Get Entity
     *
     * @return string|null
     */
    public function getEntity();

    /**
     * Get Message
     *
     * @return string|null
     */
    public function getMessage();

    /**
     * Get Additional Data
     *
     * @return string|null
     */
    public function getAdditionalData();

    /**
     * Get Created Timestamp
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Get Processed Timestamp
     *
     * @return string|null
     */
    public function getProcessedAt();

    /**
     * Set Import Id
     *
     * @param integer $importId
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ImportLineInterface
     */
    public function setImportId($importId);

    /**
     * Set Sequence Id
     *
     * @param integer $sequenceId
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ImportLineInterface
     */
    public function setSequenceId($sequenceId);

    /**
     * Set Result
     *
     * @param string $result
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ImportLineInterface
     */
    public function setResult($result);

    /**
     * Set Reference
     *
     * @param string $reference
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ImportLineInterface
     */
    public function setReference($reference);

    /**
     * Set Operation
     *
     * @param string $operation
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ImportLineInterface
     */
    public function setOperation($operation);

    /**
     * Set Entity
     *
     * @param string $entity
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ImportLineInterface
     */
    public function setEntity($entity);

    /**
     * Set Message
     *
     * @param string $message
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ImportLineInterface
     */
    public function setMessage($message);

    /**
     * Set Additional Data
     *
     * @param string $additionalData
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ImportLineInterface
     */
    public function setAdditionalData($additionalData);

    /**
     * Set Created Timestamp
     *
     * @param string $created
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ImportLineInterface
     */
    public function setCreatedAt($created);

    /**
     * Set Processed Timestamp
     *
     * @param string $processed
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ImportLineInterface
     */
    public function setProcessedAt($processed);
}