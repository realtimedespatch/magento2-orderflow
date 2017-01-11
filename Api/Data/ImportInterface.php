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
     * Set Request Id
     *
     * @param integer $requestId
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ImportInterface
     */
    public function setRequestId($requestId);

    /**
     * Set Message Id
     *
     * @param integer $messageId
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ImportInterface
     */
    public function setMessageId($messageId);

    /**
     * Set operation
     *
     * @param string $operation
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ImportInterface
     */
    public function setOperation($operation);

    /**
     * Set Entity Type
     *
     * @param string $entity
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ImportInterface
     */
    public function setEntity($entity);

    /**
     * Set Successes
     *
     * @param integer $successes
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ImportInterface
     */
    public function setSuccesses($successes);

    /**
     * Set Duplicates
     *
     * @param integer $duplicates
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ImportInterface
     */
    public function setDuplicates($duplicates);

    /**
     * Set Superseded
     *
     * @param integer $superseded
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ImportInterface
     */
    public function setSuperseded($superseded);

    /**
     * Set Failures
     *
     * @param integer $failures
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ImportInterface
     */
    public function setFailures($failures);

    /**
     * Set Created Timestamp
     *
     * @param string $created
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ImportInterface
     */
    public function setCreatedAt($created);

    /**
     * Set Viewed Timestamp
     *
     * @param string $viewed
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ImportInterface
     */
    public function setViewedAt($viewed);
}