<?php

namespace RealtimeDespatch\OrderFlow\Model\Service;

use RealtimeDespatch\OrderFlow\Api\ShipmentRequestManagementInterface;

/**
 * Class ShipmentRequestService
 *
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ShipmentRequestService implements ShipmentRequestManagementInterface
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface
     */
    protected $_builder;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_httpRequest;

    /**
     * Constructor
     *
     * @param \Magento\Framework\Registry $registry
     * @param \Psr\Log\LoggerInterface $logger
     * @param \RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface $builder
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Psr\Log\LoggerInterface $logger,
        \RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface $builder,
        \Magento\Framework\App\Request\Http $httpRequest) {
        $this->_registry = $registry;
        $this->_logger = $logger;
        $this->_builder = $builder;
        $this->_httpRequest = $httpRequest;
    }

    /**
     * @api
     * @param string $orderIncrementId
     * @param \RealtimeDespatch\OrderFlow\Api\Data\QuantityItemInterface[] $skuQty
     * @param \RealtimeDespatch\OrderFlow\Api\Data\TrackItemInterface[] $tracks
     * @param string|null $comment
     * @param string|false $email
     * @param string|false $includeComment
     * @param string|null $courierName
     * @param string|null $serviceName
     * @param string|null $trackingNumber
     * @param string|null $dateShipped
     * @param string|null $messageSeqId
     *
     * @return mixed
     */
    public function create($orderIncrementId,
                           $skuQty = [],
                           $tracks = [],
                           $comment = null,
                           $email = false,
                           $includeComment = false,
                           $courierName = null,
                           $serviceName = null,
                           $trackingNumber = null,
                           $dateShipped = null,
                           $messageSeqId = null)
    {
        try
        {
            $this->_create(
                $orderIncrementId,
                $skuQty,
                $tracks,
                $comment,
                $email,
                $includeComment,
                $courierName,
                $serviceName,
                $trackingNumber,
                $dateShipped,
                $messageSeqId
            );
        }
        catch (Exception $ex) {
            return __('Error Processing Message ').$messageSeqId;
        }

        return __('Success - Message ').$messageSeqId.__(' Received');
    }

    /**
     * @api
     * @param integer $orderIncrementId
     * @param \RealtimeDespatch\OrderFlow\Api\Data\QuantityItemInterface[] $skuQty
     * @param \RealtimeDespatch\OrderFlow\Api\Data\TrackItemInterface[] $tracks
     * @param string|null $comment
     * @param string|false $email
     * @param string|false $includeComment
     * @param string|null $courierName
     * @param string|null $serviceName
     * @param string|null $trackingNumber
     * @param string|null $dateShipped
     * @param string|null $messageSeqId
     *
     * @return mixed
     */
    public function _create(
        $orderIncrementId,
        $skuQty = [],
        $tracks = [],
        $comment = null,
        $email = false,
        $includeComment = false,
        $courierName = null,
        $serviceName = null,
        $trackingNumber = null,
        $dateShipped = null,
        $messageSeqId = null
    )
    {
        $body = array(
            'orderIncrementId' => $orderIncrementId,
            'skuQtys' => $skuQty,
            'tracks' => $tracks,
            'comment' => $comment,
            'email' => $email,
            'includeComment' => $includeComment,
            'courierName' => $courierName,
            'serviceName' => $serviceName,
            'trackingNumber' => $trackingNumber,
            'dateShipped' => $dateShipped,
            'sequenceId' => $messageSeqId
        );

        $this->_builder->setRequestData(
            \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface::TYPE_IMPORT,
            \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface::ENTITY_SHIPMENT,
            \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface::OP_CREATE,
            $messageSeqId
        );

        $this->_builder->setRequestBody($this->_httpRequest->getContent());
        $this->_builder->addRequestLine(json_encode($body), $messageSeqId);
        $this->_builder->saveRequest();

        // Register request to capture response later.
        $this->_registry->register(
            'request_id',
            $this->_builder->getRequest()->getId()
        );
    }
}