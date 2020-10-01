<?php

namespace RealtimeDespatch\OrderFlow\Model;

use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;

class Request extends \Magento\Framework\Model\AbstractModel implements RequestInterface
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'rtd_request';

    /**
     * Request Lines.
     *
     * @var array
     */
    protected $_lines;

    /**
     * @var RealtimeDespatch\OrderFlow\Model\ResourceModel\RequestLine\CollectionFactory
     */
    protected $_requestLineFactory;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \RealtimeDespatch\OrderFlow\Model\RequestLineFactory $requestLineFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \RealtimeDespatch\OrderFlow\Model\RequestLineFactory $requestLineFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_requestLineFactory = $requestLineFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('RealtimeDespatch\OrderFlow\Model\ResourceModel\Request');
        $this->_lines = array();
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::REQUEST_ID);
    }

    /**
     * Get Request Lines
     *
     * @return array
     */
    public function getLines()
    {
        if ($this->_lines) {
            return $this->_lines;
        }

        $this->_lines = $this->_requestLineFactory
            ->create()
            ->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('request_id', ['eq' => $this->getId()])
            ->loadData();

        return $this->_lines;
    }

    /**
     * Get Message ID
     *
     * @return int|null
     */
    public function getMessageId()
    {
        return $this->getData(self::MESSAGE_ID);
    }

    /**
     * Get Scope ID
     *
     * @return int|null
     */
    public function getScopeId()
    {
        return $this->getData(self::SCOPE_ID);
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
     * Get Entity
     *
     * @return string|null
     */
    public function getEntity()
    {
        return $this->getData(self::ENTITY);
    }

    /**
     * Get Operation
     *
     * @return string|null
     */
    public function getOperation()
    {
        return $this->getData(self::OPERATION);
    }

    /**
     * Get Request Body
     *
     * @return string|null
     */
    public function getRequestBody()
    {
        if ( ! $this->getData(self::REQUEST_BODY)) {
            return $this->getProcessedAt() ?  __('Request Unavailable') : _('Pending');
        }

        $xml = '';

        try {
            $dom = new \DOMDocument;
            $dom->preserveWhiteSpace = false;
            $dom->loadXML(gzinflate($this->getData(self::REQUEST_BODY)));
            $dom->formatOutput = true;
            $xml = $dom->saveXml();
        }
        catch (\Exception $ex) {
            $xml = __('Request Unavailable');
        }

        return $xml;
    }

    /**
     * Get Response Body
     *
     * @return string|null
     */
    public function getResponseBody()
    {
        if ( ! $this->getData(self::RESPONSE_BODY)) {
            return $this->getProcessedAt() ?  __('Response Unavailable') : _('Pending');
        }

        $xml = '';

        try {
            $dom = new \DOMDocument;
            $dom->preserveWhiteSpace = false;
            $dom->loadXML(gzinflate($this->getData(self::RESPONSE_BODY)));
            $dom->formatOutput = true;
            $xml = $dom->saveXml();
        }
        catch (\Exception $ex) {
            $xml = gzinflate($this->getData(self::RESPONSE_BODY));
        }

        return $xml;
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
     * Adds a line to the request.
     *
     * @param \RealtimeDespatch\OrderFlow\Api\Data\RequestLineInterface $line
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface
     */
    public function addLine(\RealtimeDespatch\OrderFlow\Api\Data\RequestLineInterface $line)
    {
        $line->setRequest($this);
        $this->_lines[] = $line;
    }

    /**
     * Set Request Lines
     *
     * @param array $lines
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface
     */
    public function setLines($lines)
    {
        $this->_lines = $lines;

        return $this;
    }

    /**
     * Set Message Id
     *
     * @param string $messageId
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface
     */
    public function setMessageId($messageId)
    {
        return $this->setData(self::MESSAGE_ID, $messageId);
    }

    /**
     * Set Scope Id
     *
     * @param string $scopeId
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface
     */
    public function setScopeId($scopeId)
    {
        return $this->setData(self::SCOPE_ID, $scopeId);
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * Set entity
     *
     * @param string $entity
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface
     */
    public function setEntity($entity)
    {
        return $this->setData(self::ENTITY, $entity);
    }

    /**
     * Set operation
     *
     * @param string $operation
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface
     */
    public function setOperation($operation)
    {
        return $this->setData(self::OPERATION, $operation);
    }

    /**
     * Set request body
     *
     * @param string $requestBody
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface
     */
    public function setRequestBody($requestBody)
    {
        return $this->setData(self::REQUEST_BODY, gzdeflate($requestBody, 9));
    }

    /**
     * Set response body
     *
     * @param string $responseBody
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface
     */
    public function setResponseBody($responseBody)
    {
        return $this->setData(self::RESPONSE_BODY, gzdeflate($responseBody, 9));
    }

    /**
     * Set created timestamp
     *
     * @param string $created
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface
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
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface
     */
    public function setProcessedAt($processed)
    {
        foreach ($this->getLines() as $line) {
            $line->setProcessedAt($processed);
        }

        return $this->setData(self::PROCESSED_AT, $processed);
    }

    /**
     * Checks whether the request can be processed.
     *
     * @return boolean
     */
    public function canProcess()
    {
        return ! $this->isProcessed();
    }

    /**
     * Checks whether the request has been processed.
     *
     * @return boolean
     */
    public function isProcessed()
    {
        return ! is_null($this->getProcessedAt());
    }

    /**
     * Checks whether the request is an export.
     *
     * @return boolean
     */
    public function isExport()
    {
        return $this->getType() === self::TYPE_EXPORT;
    }

    /**
     * Checks whether the request is an import.
     *
     * @return boolean
     */
    public function isImport()
    {
        return $this->getType() === self::TYPE_IMPORT;
    }

    /**
     * Checks whether this is a create operation.
     *
     * @return boolean
     */
    public function isCreate()
    {
        return $this->getOperation() === self::OP_CREATE;
    }
}