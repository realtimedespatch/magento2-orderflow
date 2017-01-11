<?php

namespace RealtimeDespatch\OrderFlow\Model;

use RealtimeDespatch\OrderFlow\Api\Data\RequestLineInterface;
use Magento\Framework\DataObject\IdentityInterface;

class RequestLine extends \Magento\Framework\Model\AbstractModel implements RequestLineInterface, IdentityInterface
{
    /**
     * CMS page cache tag
     */
    const CACHE_TAG = 'rtd_request_line';

    /**
     * @var string
     */
    protected $_cacheTag = 'rtd_request_line';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'rtd_request_line';

    /**
     * Linked Request.
     *
     * @var RealtimeDespatch\OrderFlow\Model\Request
     */
    protected $_request;

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('RealtimeDespatch\OrderFlow\Model\ResourceModel\RequestLine');
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
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get Request
     *
     * @return \RealtimeDespatch\OrderFlow\Model\Request|null
     */
    public function getRequest()
    {
        return $this->_request;
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
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestLineInterface
     */
    public function setMessageId($messageId)
    {
        return $this->setData(self::MESSAGE_ID, $id);
    }

    /**
     * Set request
     *
     * @param \RealtimeDespatch\OrderFlow\Model\Request $request
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestLineInterface
     */
    public function setRequest($request)
    {
        $this->setType($request->getEntity());

        return $this->_request = $request;
    }

    /**
     * Set request id
     *
     * @param string $requestId
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestLineInterface
     */
    public function setRequestId($requestId)
    {
        return $this->setData(self::REQUEST_ID, $requestId);
    }

    /**
     * Set sequence id
     *
     * @param string $sequenceId
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestLineInterface
     */
    public function setSequenceId($sequenceId)
    {
        return $this->setData(self::SEQUENCE_ID, $sequenceId);
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestLineInterface
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }
    /**
     * Set response
     *
     * @param string $response
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestLineInterface
     */
    public function setResponse($response)
    {
        return $this->setData(self::RESPONSE, $response);
    }

    /**
     * Set body
     *
     * @param string $body
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestLineInterface
     */
    public function setBody($body)
    {
        return $this->setData(self::BODY, $body);
    }

    /**
     * Set created timestamp
     *
     * @param string $created
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestLineInterface
     */
    public function setCreatedAt($created)
    {
        return $this->setData(self::CREATED_AT, $created);
    }

    /**
     * Set processed timestamp
     *
     * @param string $processed
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestLineInterface
     */
    public function setProcessedAt($processed)
    {
        return $this->setData(self::PROCESSED_AT, $processed);
    }
}