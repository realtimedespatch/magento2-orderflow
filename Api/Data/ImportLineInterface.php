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
     * @return ImportLineInterface
     */
    public function setImportId(int $importId);

    /**
     * Set Sequence Id
     *
     * @param string $sequenceId
     *
     * @return ImportLineInterface
     */
    public function setSequenceId(string $sequenceId);

    /**
     * Set Result
     *
     * @param string $result
     *
     * @return ImportLineInterface
     */
    public function setResult(string $result);

    /**
     * Set Reference
     *
     * @param string $reference
     *
     * @return ImportLineInterface
     */
    public function setReference(string $reference);

    /**
     * Set Operation
     *
     * @param string $operation
     *
     * @return ImportLineInterface
     */
    public function setOperation(string $operation);

    /**
     * Set Entity
     *
     * @param string $entity
     *
     * @return ImportLineInterface
     */
    public function setEntity(string $entity);

    /**
     * Set Message
     *
     * @param string $message
     *
     * @return ImportLineInterface
     */
    public function setMessage(string $message);

    /**
     * Set Additional Data
     *
     * @param string $additionalData
     *
     * @return ImportLineInterface
     */
    public function setAdditionalData(string $additionalData);

    /**
     * Set Created Timestamp
     *
     * @param string $created
     *
     * @return ImportLineInterface
     */
    public function setCreatedAt(string $created);

    /**
     * Set Processed Timestamp
     *
     * @param string $processed
     *
     * @return ImportLineInterface
     */
    public function setProcessedAt(string $processed);
}
