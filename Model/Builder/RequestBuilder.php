<?php

namespace RealtimeDespatch\OrderFlow\Model\Builder;

use RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface;

class RequestBuilder implements RequestBuilderInterface
{
    /**
     * @var \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface
     */
    protected $_request;

    /**
     * @var \RealtimeDespatch\OrderFlow\Model\RequestFactory
     */
    protected $_requestFactory;

    /**
     * @var \RealtimeDespatch\OrderFlow\Model\RequestLineFactory
     */
    protected $_requestLineFactory;

    /**
     * @var \RealtimeDespatch\OrderFlow\Api\RequestRepositoryInterface
     */
    protected $_requestRespository;

    /**
     * @param \RealtimeDespatch\OrderFlow\Model\RequestFactory $requestFactory
     * @param \RealtimeDespatch\OrderFlow\Model\RequestLineFactory $requestLineFactory
     * @param \RealtimeDespatch\OrderFlow\Api\RequestRepositoryInterface $requestRepository
     */
    public function __construct(
        \RealtimeDespatch\OrderFlow\Model\RequestFactory $requestFactory,
        \RealtimeDespatch\OrderFlow\Model\RequestLineFactory $requestLineFactory,
        \RealtimeDespatch\OrderFlow\Api\RequestRepositoryInterface $requestRepository
    ) {
        $this->_requestFactory = $requestFactory;
        $this->_requestLineFactory = $requestLineFactory;
        $this->_requestRespository = $requestRepository;
        $this->_request = $this->_requestFactory->create();
    }

    /**
     * Sets the request data.
     *
     * @param string $type Request Type
     * @param string $entity Request Entity
     * @param string $operation Request Operation
     * @param string $messageId Message Id
     *
     * @return \RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface
     */
    public function setRequestData($type, $entity, $operation, $messageId = null)
    {
        $this->_request->setType($type);
        $this->_request->setEntity($entity);
        $this->_request->setOperation($operation);

        if ($messageId) {
            $this->_request->setMessageId($messageId);
        }

        return $this;
    }

    /**
     * Adds a request line to the request.
     *
     * @param string $body Request Line Body
     * @param string|null $sequenceId Sequence ID
     *
     * @return \RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface
     */
    public function addRequestLine($body, $sequenceId = null)
    {
        $line = $this->_requestLineFactory->create();

        $line->setBody($body);
        $line->setSequenceId($sequenceId);
        $this->_request->addLine($line);

        return $this;
    }

    /**
     * Sets the processed date.
     *
     * @param string $processed
     *
     * @return \RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface
     */
    public function markProcessed($processed)
    {
        $this->_request->setCreatedAt($processed);
        $this->_request->setProcessedAt($processed);
    }

    /**
     * Sets the request body.
     *
     * @param string $body
     *
     * @return \RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface
     */
    public function setRequestBody($body)
    {
        $this->_request->setRequestBody($body);

        return $this;
    }

    /**
     * Sets the response body.
     *
     * @param string $body
     *
     * @return \RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface
     */
    public function setResponseBody($body)
    {
        $this->_request->setResponseBody($body);

        return $this;
    }

    /**
     * Sets the Scope ID.
     *
     * @param string $scopeId
     *
     * @return \RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface
     */
    public function setScopeId($scopeId)
    {
        $this->_request->setScopeId($scopeId);

        return $this;
    }

    /**
     * Resets the builder.
     *
     * @return \RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface
     */
    public function resetBuilder()
    {
        $this->_request = $this->_requestFactory->create();

        return $this;
    }

    /**
     * Returns a new request instance.
     *
     * @return RealtimeDespatch\OrderFlow\Api\Data\RequestInterface
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Returns a new request instance.
     *
     * @return RealtimeDespatch\OrderFlow\Api\Data\RequestInterface
     */
    public function saveRequest()
    {
        return $this->_requestRespository->save($this->_request);
    }
}