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
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestLineInterface
     */
    public function setRequestId($requestId);

    /**
     * Set sequence ID
     *
     * @param string $sequenceId
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestLineInterface
     */
    public function setSequenceId($sequenceId);

    /**
     * Set type
     *
     * @param string $type
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestLineInterface
     */
    public function setType($type);

    /**
     * Set response
     *
     * @param string $response
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestLineInterface
     */
    public function setResponse($response);

    /**
     * Set body
     *
     * @param string $body
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestLineInterface
     */
    public function setBody($body);

    /**
     * Set created timestamp
     *
     * @param string $created
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestLineInterface
     */
    public function setCreatedAt($created);

    /**
     * Set processed timestamp
     *
     * @param string $processed
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestLineInterface
     */
    public function setProcessedAt($processed);
}