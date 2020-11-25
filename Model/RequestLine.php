<?php

namespace RealtimeDespatch\OrderFlow\Model;

use Magento\Framework\Model\AbstractModel;
use RealtimeDespatch\OrderFlow\Api\Data\RequestLineInterface;

class RequestLine extends AbstractModel implements RequestLineInterface
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'rtdrequest_line';

    /**
     * Linked Request.
     *
     * @var Request
     */
    protected $request;

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\RequestLine::class);
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::LINE_ID);
    }

    /**
     * Get Request
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Get Request ID
     *
     * @return int|null
     */
    public function getRequestId()
    {
        return $this->getData(self::REQUEST_ID);
    }

    /**
     * Get Sequence ID
     *
     * @return int|null
     */
    public function getSequenceId()
    {
        return $this->getData(self::SEQUENCE_ID);
    }

    /**
     * Get Type
     *
     * @return string|null
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * Get Response
     *
     * @return string|null
     */
    public function getResponse()
    {
        return $this->getData(self::RESPONSE);
    }

    /**
     * Get Body
     *
     * @return string|null
     */
    public function getBody()
    {
        return json_decode($this->getData(self::BODY));
    }

    /**
     * Get created timestamp
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Get processed timestamp
     *
     * @return string|null
     */
    public function getProcessedAt()
    {
        return $this->getData(self::PROCESSED_AT);
    }

    /**
     * Set type
     *
     * @param string $messageId
     *
     * @return RequestLineInterface
     */
    public function setMessageId(string $messageId)
    {
        return $this->setData(self::MESSAGE_ID, $messageId);
    }

    /**
     * Set request
     *
     * @param Request $request
     *
     * @return Request
     */
    public function setRequest(Request $request)
    {
        $this->setType($request->getEntity());

        return $this->request = $request;
    }

    /**
     * Set request id
     *
     * @param string $requestId
     *
     * @return RequestLineInterface
     */
    public function setRequestId(string $requestId)
    {
        return $this->setData(self::REQUEST_ID, $requestId);
    }

    /**
     * Set sequence id
     *
     * @param string $sequenceId
     *
     * @return RequestLineInterface
     */
    public function setSequenceId(string $sequenceId)
    {
        return $this->setData(self::SEQUENCE_ID, $sequenceId);
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return RequestLineInterface
     */
    public function setType(string $type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * Set response
     *
     * @param string $response
     *
     * @return RequestLineInterface
     */
    public function setResponse(string $response)
    {
        return $this->setData(self::RESPONSE, $response);
    }

    /**
     * Set body
     *
     * @param string $body
     *
     * @return RequestLineInterface
     */
    public function setBody(string $body)
    {
        return $this->setData(self::BODY, $body);
    }

    /**
     * Set created timestamp
     *
     * @param string $created
     *
     * @return RequestLineInterface
     */
    public function setCreatedAt(string $created)
    {
        return $this->setData(self::CREATED_AT, $created);
    }

    /**
     * Set processed timestamp
     *
     * @param string $processed
     *
     * @return RequestLineInterface
     */
    public function setProcessedAt(string $processed)
    {
        return $this->setData(self::PROCESSED_AT, $processed);
    }
}
