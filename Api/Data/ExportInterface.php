<?php

namespace RealtimeDespatch\OrderFlow\Api\Data;

interface ExportInterface
{
    /* Params */
    const EXPORT_ID   = 'export_id';
    const REQUEST_ID  = 'request_id';
    const MESSAGE_ID  = 'message_id';
    const SCOPE_ID    = 'scope_id';
    const ENTITY      = 'entity';
    const OPERATION   = 'operation';
    const SUCCESSES   = 'successes';
    const DUPLICATES  = 'duplicates';
    const SUPERSEDED  = 'superseded';
    const FAILURES    = 'failures';
    const CREATED_AT  = 'created_at';
    const VIEWED_AT   = 'viewed_at';

    /* Entity Types */
    const ENTITY_ORDER   = 'Order';
    const ENTITY_PRODUCT = 'Product';

    /* Request Operations */
    const OP_EXPORT = 'Export';
    const OP_CREATE = 'Create';
    const OP_UPDATE = 'Update';
    const OP_CANCEL = 'Cancel';

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
     * Get Request Lines
     *
     * @return array
     */
    public function getLines();

    /**
     * Get Message ID
     *
     * @return int|null
     */
    public function getMessageId();

    /**
     * Get Scope ID
     *
     * @return int|null
     */
    public function getScopeId();

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
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportInterface
     */
    public function setRequestId($requestId);

    /**
     * Adds a line to the export.
     *
     * @param \RealtimeDespatch\OrderFlow\Api\Data\ExportLineInterface $line
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportInterface
     */
    public function addLine(\RealtimeDespatch\OrderFlow\Api\Data\ExportLineInterface $line);

    /**
     * Set Export Lines
     *
     * @param array $lines
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportInterface
     */
    public function setLines($lines);

    /**
     * Set Message Id
     *
     * @param integer $messageId
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportInterface
     */
    public function setMessageId($messageId);

    /**
     * Set Scope Id
     *
     * @param integer $scopeId
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportInterface
     */
    public function setScopeId($scopeId);

    /**
     * Set operation
     *
     * @param string $operation
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportInterface
     */
    public function setOperation($operation);

    /**
     * Set Entity Type
     *
     * @param string $entity
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportInterface
     */
    public function setEntity($entity);

    /**
     * Set Successes
     *
     * @param integer $successes
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportInterface
     */
    public function setSuccesses($successes);

    /**
     * Set Duplicates
     *
     * @param integer $duplicates
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportInterface
     */
    public function setDuplicates($duplicates);

    /**
     * Set Superseded
     *
     * @param integer $superseded
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportInterface
     */
    public function setSuperseded($superseded);

    /**
     * Set Failures
     *
     * @param integer $failures
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportInterface
     */
    public function setFailures($failures);

    /**
     * Set Created Timestamp
     *
     * @param string $created
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportInterface
     */
    public function setCreatedAt($created);

    /**
     * Set Viewed Timestamp
     *
     * @param string $viewed
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportInterface
     */
    public function setViewedAt($viewed);

    /**
     * Checks whether this is a product export.
     *
     * @return boolean
     */
    public function isProductExport();

    /**
     * Checks whether this is an order export.
     *
     * @return boolean
     */
    public function isOrderExport();
}