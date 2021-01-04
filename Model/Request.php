<?php /** @noinspection ALL */

namespace RealtimeDespatch\OrderFlow\Model;

use DOMDocument;
use Exception;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use RealtimeDespatch\OrderFlow\Api\Data\RequestLineInterface;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\RequestLine\CollectionFactory as RequestLineCollectionFactory;
use RealtimeDespatch\OrderFlow\Helper\Xml as XmlHelper;

/**
 * Class Request
 *
 * Representation of an API Request.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Request extends AbstractModel implements RequestInterface
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
    protected $lines = null;

    /**
     * @var XmlHelper
     */
    protected $xmlHelper;

    /**
     * @var RequestLineCollectionFactory
     */
    protected $requestLineCollectionFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param RequestLineCollectionFactory $requestLineCollectionFactory
     * @param XmlHelper $xmlHelper
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        RequestLineCollectionFactory $requestLineCollectionFactory,
        XmlHelper $xmlHelper,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->requestLineCollectionFactory = $requestLineCollectionFactory;
        $this->xmlHelper = $xmlHelper;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initiliaze.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\RealtimeDespatch\OrderFlow\Model\ResourceModel\Request::class);
    }

    /**
     * Get ID
     *
     * @return int
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
         if (! is_null($this->lines)) {
            return $this->lines;
        }

        $this->lines = $this
            ->requestLineCollectionFactory
            ->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('request_id', ['eq' => $this->getId()])
            ->loadData();

        return $this->lines;
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
        if (! $this->getData(self::REQUEST_BODY)) {
            return $this->getProcessedAt() ?  __('Request Unavailable') : _('Pending');
        }

        try {
            $xml = gzinflate($this->getData(self::REQUEST_BODY));
            $dom = $this->xmlHelper->getDomDocument($xml);
            return $dom->saveXML();
        } catch (Exception $ex) {
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
        if (! $this->getData(self::RESPONSE_BODY)) {
            return $this->getProcessedAt() ?  __('Response Unavailable') : _('Pending');
        }

        try {
            $xml = gzinflate($this->getData(self::RESPONSE_BODY));
            $dom = $this->xmlHelper->getDomDocument($xml);
            return $dom->saveXML();
        } catch (Exception $ex) {
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
     * @param RequestLineInterface $line
     *
     * @return RequestInterface
     */
    public function addLine(RequestLineInterface $line)
    {
        $line->setRequest($this);
        $this->lines[] = $line;

        return $this;
    }

    /**
     * Set Request Lines
     *
     * @param mixed $lines
     *
     * @return RequestInterface
     */
    public function setLines($lines)
    {
        $this->lines = $lines;

        return $this;
    }

    /**
     * Set Message Id
     *
     * @param string $messageId
     *
     * @return RequestInterface
     */
    public function setMessageId(string $messageId)
    {
        return $this->setData(self::MESSAGE_ID, $messageId);
    }

    /**
     * Set Scope Id
     *
     * @param integer|null $scopeId
     *
     * @return RequestInterface
     */
    public function setScopeId(int $scopeId = null)
    {
        return $this->setData(self::SCOPE_ID, $scopeId);
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return RequestInterface
     */
    public function setType(string $type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * Set entity
     *
     * @param string $entity
     *
     * @return RequestInterface
     */
    public function setEntity(string $entity)
    {
        return $this->setData(self::ENTITY, $entity);
    }

    /**
     * Set operation
     *
     * @param string $operation
     *
     * @return RequestInterface
     */
    public function setOperation(string $operation)
    {
        return $this->setData(self::OPERATION, $operation);
    }

    /**
     * Set request body
     *
     * @param string $requestBody
     *
     * @return RequestInterface
     */
    public function setRequestBody(string $requestBody)
    {
        return $this->setData(self::REQUEST_BODY, gzdeflate($requestBody, 9));
    }

    /**
     * Set response body
     *
     * @param string $responseBody
     *
     * @return RequestInterface
     */
    public function setResponseBody(string $responseBody)
    {
        return $this->setData(self::RESPONSE_BODY, gzdeflate($responseBody, 9));
    }

    /**
     * Set created timestamp
     *
     * @param string $created
     *
     * @return RequestInterface
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
     * @return RequestInterface
     */
    public function setProcessedAt(string $processed)
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
