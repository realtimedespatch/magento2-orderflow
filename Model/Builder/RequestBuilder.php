<?php

/** @noinspection PhpUndefinedClassInspection */

namespace RealtimeDespatch\OrderFlow\Model\Builder;

use Magento\Framework\Exception\CouldNotSaveException;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface;
use RealtimeDespatch\OrderFlow\Api\RequestRepositoryInterface;
use RealtimeDespatch\OrderFlow\Model\Request;
use RealtimeDespatch\OrderFlow\Model\RequestFactory;
use RealtimeDespatch\OrderFlow\Model\RequestLineFactory;

class RequestBuilder implements RequestBuilderInterface
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var RequestFactory
     */
    protected $requestFactory;

    /**
     * @var RequestLineFactory
     */
    protected $requestLineFactory;

    /**
     * @var RequestRepositoryInterface
     */
    protected $requestRepository;

    /**
     * @param RequestFactory $requestFactory
     * @param RequestLineFactory $requestLineFactory
     * @param RequestRepositoryInterface $requestRepository
     */
    public function __construct(
        RequestFactory $requestFactory,
        RequestLineFactory $requestLineFactory,
        RequestRepositoryInterface $requestRepository
    ) {
        $this->requestFactory = $requestFactory;
        $this->requestLineFactory = $requestLineFactory;
        $this->requestRepository = $requestRepository;

        /** @noinspection PhpUndefinedMethodInspection */
        $this->request = $this->requestFactory->create();
    }

    /**
     * Sets the request data.
     *
     * @param string $type Request Type
     * @param string $entity Request Entity
     * @param string $operation Request Operation
     * @param null $messageId Message Id
     *
     * @return RequestBuilderInterface
     */
    public function setRequestData(string $type, string $entity, string $operation, $messageId = null)
    {
        $this->request->setType($type);
        $this->request->setEntity($entity);
        $this->request->setOperation($operation);

        if ($messageId) {
            $this->request->setMessageId($messageId);
        }

        return $this;
    }

    /**
     * Adds a request line to the request.
     *
     * @param string $body Request Line Body
     * @param string|null $sequenceId Sequence ID
     *
     * @return RequestBuilderInterface
     */
    public function addRequestLine(string $body, $sequenceId = null)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $line = $this->requestLineFactory->create();

        $line->setBody($body);
        $line->setSequenceId($sequenceId);
        $this->request->addLine($line);

        return $this;
    }

    /**
     * Sets the processed date.
     *
     * @param string $processed
     *
     * @return RequestBuilderInterface
     */
    public function markProcessed(string $processed)
    {
        $this->request->setCreatedAt($processed);
        $this->request->setProcessedAt($processed);

        return $this;
    }

    /**
     * Sets the request body.
     *
     * @param string $body
     *
     * @return RequestBuilderInterface
     */
    public function setRequestBody(string $body)
    {
        $this->request->setRequestBody($body);

        return $this;
    }

    /**
     * Sets the response body.
     *
     * @param string $body
     *
     * @return RequestBuilderInterface
     */
    public function setResponseBody(string $body)
    {
        $this->request->setResponseBody($body);

        return $this;
    }

    /**
     * Sets the Scope ID.
     *
     * @param string $scopeId
     *
     * @return RequestBuilderInterface
     */
    public function setScopeId(string $scopeId)
    {
        $this->request->setScopeId($scopeId);

        return $this;
    }

    /**
     * Resets the builder.
     *
     * @return RequestBuilderInterface
     */
    public function resetBuilder()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->request = $this->requestFactory->create();

        return $this;
    }

    /**
     * Returns a new request instance.
     *
     * @return RequestInterface|Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Returns a new request instance.
     *
     * @return RequestInterface
     * @throws CouldNotSaveException
     */
    public function saveRequest()
    {
        return $this->requestRepository->save($this->request);
    }
}
