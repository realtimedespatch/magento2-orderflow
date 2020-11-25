<?php

namespace RealtimeDespatch\OrderFlow\Api\Data;

interface ImportInterface
{
    /* Params */
    const IMPORT_ID     = 'import_id';
    const REQUEST_ID    = 'request_id';
    const MESSAGE_ID    = 'message_id';
    const ENTITY        = 'entity';
    const OPERATION     = 'operation';
    const SUCCESSES     = 'successes';
    const DUPLICATES    = 'duplicates';
    const SUPERSEDED    = 'superseded';
    const FAILURES      = 'failures';
    const CREATED_AT    = 'created_at';
    const VIEWED_AT     = 'viewed_at';
    const LINES         = 'lines';

    /* Request Operations */
    const OP_EXPORT = 'Export';
    const OP_CREATE = 'Create';
    const OP_UPDATE = 'Update';
    const OP_CANCEL = 'Cancel';

    /* Entity Types */
    const ENTITY_INVENTORY  = 'Inventory';
    const ENTITY_SHIPMENT = 'Shipment';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get Request ID
     *
     * @return int|null
     */
    public function getRequestId();

    /**
     * Get Message ID
     *
     * @return int|null
     */
    public function getMessageId();

    /**
     * Get Operation
     *
     * @return string|null
     */
    public function getOperation();

    /**
     * Get Entity Type.
     *
     * @return string|null
     */
    public function getEntity();

    /**
     * Get Successes
     *
     * @return int|null
     */
    public function getSuccesses();

    /**
     * Get Duplicates
     *
     * @return int|null
     */
    public function getDuplicates();

    /**
     * Get Superseded
     *
     * @return int|null
     */
    public function getSuperseded();

    /**
     * Get Failures
     *
     * @return int|null
     */
    public function getFailures();

    /**
     * Get Created Timestamp
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Get Viewed Timestamp
     *
     * @return string|null
     */
    public function getViewedAt();

    /**
     * Get Lines
     *
     * @return mixed
     */
    public function getLines();

    /**
     * Set Request Id
     *
     * @param integer $requestId
     *
     * @return ImportInterface
     */
    public function setRequestId(int $requestId);

    /**
     * Set Message Id
     *
     * @param string $messageId
     *
     * @return ImportInterface
     */
    public function setMessageId(string $messageId);

    /**
     * Set operation
     *
     * @param string $operation
     *
     * @return ImportInterface
     */
    public function setOperation(string $operation);

    /**
     * Set Entity Type
     *
     * @param string $entity
     *
     * @return ImportInterface
     */
    public function setEntity(string $entity);

    /**
     * Set Successes
     *
     * @param integer $successes
     *
     * @return ImportInterface
     */
    public function setSuccesses(int $successes);

    /**
     * Set Duplicates
     *
     * @param integer $duplicates
     *
     * @return ImportInterface
     */
    public function setDuplicates(int $duplicates);

    /**
     * Set Superseded
     *
     * @param integer $superseded
     *
     * @return ImportInterface
     */
    public function setSuperseded(int $superseded);

    /**
     * Set Failures
     *
     * @param integer $failures
     *
     * @return ImportInterface
     */
    public function setFailures(int $failures);

    /**
     * Set Created Timestamp
     *
     * @param string $created
     *
     * @return ImportInterface
     */
    public function setCreatedAt(string $created);

    /**
     * Set Viewed Timestamp
     *
     * @param string $viewed
     *
     * @return ImportInterface
     */
    public function setViewedAt(string $viewed);

    /**
     * Set Lines
     *
     * @param mixed $lines
     * @return mixed
     */
    public function setLines($lines);
}
