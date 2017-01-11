<?php

namespace RealtimeDespatch\OrderFlow\Api\Data;

interface RequestInterface
{
    /* Request Params */
    const REQUEST_ID    = 'request_id';
    const MESSAGE_ID    = 'message_id';
    const SCOPE_ID      = 'scope_id';
    const TYPE          = 'type';
    const ENTITY        = 'entity';
    const OPERATION     = 'operation';
    const REQUEST_BODY  = 'request_body';
    const RESPONSE_BODY = 'response_body';
    const CREATED_AT    = 'created_at';
    const PROCESSED_AT  = 'processed_at';

    /* Request Types */
    const TYPE_EXPORT = 'Export';
    const TYPE_IMPORT = 'Import';

    /* Request Operations */
    const OP_EXPORT = 'Export';
    const OP_CREATE = 'Create';
    const OP_UPDATE = 'Update';
    const OP_CANCEL = 'Cancel';

    /* Entity Types */
    const ENTITY_PRODUCT = 'Product';
    const ENTITY_ORDER = 'Order';
    const ENTITY_SHIPMENT = 'Shipment';
    const ENTITY_INVENTORY = 'Inventory';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

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
     * Get Type
     *
     * @return string|null
     */
    public function getType();

    /**
     * Get Entity
     *
     * @return string|null
     */
    public function getEntity();

    /**
     * Get Operation
     *
     * @return string|null
     */
    public function getOperation();

    /**
     * Get Request Body
     *
     * @return string|null
     */
    public function getRequestBody();

    /**
     * Get Response Body
     *
     * @return string|null
     */
    public function getResponseBody();

    /**
     * Get created timestamp
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Get processed timestamp
     *
     * @return string|null
     */
    public function getProcessedAt();

    /**
     * Adds a line to the request.
     *
     * @param \RealtimeDespatch\OrderFlow\Api\Data\RequestLineInterface $line
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface
     */
    public function addLine(\RealtimeDespatch\OrderFlow\Api\Data\RequestLineInterface $line);

    /**
     * Set Request Lines
     *
     * @param array $lines
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface
     */
    public function setLines($lines);

    /**
     * Set Message Id
     *
     * @param string $messageId
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface
     */
    public function setMessageId($messageId);

    /**
     * Set Scope Id
     *
     * @param string $scopeId
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface
     */
    public function setScopeId($scopeId);

    /**
     * Set type
     *
     * @param string $type
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface
     */
    public function setType($type);

    /**
     * Set entity
     *
     * @param string $entity
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface
     */
    public function setEntity($entity);

    /**
     * Set operation
     *
     * @param string $operation
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface
     */
    public function setOperation($operation);

    /**
     * Set request body
     *
     * @param string $requestBody
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface
     */
    public function setRequestBody($requestBody);

    /**
     * Set response body
     *
     * @param string $responseBody
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface
     */
    public function setResponseBody($responseBody);

    /**
     * Set created timestamp
     *
     * @param string $created
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface
     */
    public function setCreatedAt($created);

    /**
     * Set processed timestamp
     *
     * @param string $processed
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface
     */
    public function setProcessedAt($processed);

    /**
     * Checks whether the request can be processed.
     *
     * @return boolean
     */
    public function canProcess();

    /**
     * Checks whether the request has been processed.
     *
     * @return boolean
     */
    public function isProcessed();

    /**
     * Checks whether the request is an export.
     *
     * @return boolean
     */
    public function isExport();

    /**
     * Checks whether the request is an import.
     *
     * @return boolean
     */
    public function isImport();
}