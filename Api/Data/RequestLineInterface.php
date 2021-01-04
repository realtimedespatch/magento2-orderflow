<?php

namespace RealtimeDespatch\OrderFlow\Api\Data;

interface RequestLineInterface
{
    const LINE_ID      = 'line_id';
    const REQUEST_ID   = 'request_id';
    const SEQUENCE_ID  = 'sequence_id';
    const TYPE         = 'type';
    const RESPONSE     = 'response';
    const BODY         = 'body';
    const CREATED_AT   = 'created_at';
    const PROCESSED_AT = 'processed_at';
    const MESSAGE_ID  = 'message_id';

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
     * Get Sequence ID
     *
     * @return int|null
     */
    public function getSequenceId();

    /**
     * Get Type
     *
     * @return string|null
     */
    public function getType();

    /**
     * Get Response
     *
     * @return string|null
     */
    public function getResponse();

    /**
     * Get Body
     *
     * @return string|null
     */
    public function getBody();

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
     * Set request ID
     *
     * @param string $requestId
     *
     * @return RequestLineInterface
     */
    public function setRequestId(string $requestId);

    /**
     * Set sequence id
     *
     * @param string|null $sequenceId
     *
     * @return RequestLineInterface
     */
    public function setSequenceId(string $sequenceId = null);

    /**
     * Set type
     *
     * @param string $type
     *
     * @return RequestLineInterface
     */
    public function setType(string $type);

    /**
     * Set response
     *
     * @param string $response
     *
     * @return RequestLineInterface
     */
    public function setResponse(string $response);

    /**
     * Set body
     *
     * @param string $body
     *
     * @return RequestLineInterface
     */
    public function setBody(string $body);

    /**
     * Set created timestamp
     *
     * @param string $created
     *
     * @return RequestLineInterface
     */
    public function setCreatedAt(string $created);

    /**
     * Set processed timestamp
     *
     * @param string $processed
     *
     * @return RequestLineInterface
     */
    public function setProcessedAt(string $processed);
}
