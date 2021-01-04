<?php

namespace RealtimeDespatch\OrderFlow\Api;

use Magento\Framework\Exception\CouldNotSaveException;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;

/**
 * Request Builder Interface.
 *
 * Defines the methods available for a request builder.
 *
 * @api
 */
interface RequestBuilderInterface
{
    /**
     * Sets the Scope ID.
     *
     * @param string $scopeId
     *
     * @return RequestBuilderInterface
     */
    public function setScopeId(string $scopeId);

    /**
     * Adds a request line to the request.
     *
     * @param string $body Request Line Body
     * @param string|null $sequenceId Sequence ID
     *
     * @return RequestBuilderInterface
     */
    public function addRequestLine(string $body, $sequenceId = null);

    /**
     * Sets the request body.
     *
     * @param string $body
     *
     * @return RequestBuilderInterface
     */
    public function setRequestBody(string $body);

    /**
     * Sets the response body.
     *
     * @param string $body
     *
     * @return RequestBuilderInterface
     */
    public function setResponseBody(string $body);

    /**
     * Returns a new request instance.
     *
     * @param string $type
     * @param string $entity
     * @param string $operation
     * @param null $messageId
     * @param array $lines
     * @return RequestInterface
     * @throws CouldNotSaveException
     */
    public function saveRequest(
        string $type,
        string $entity,
        string $operation,
        $messageId = null,
        array $lines = []
    );
}
