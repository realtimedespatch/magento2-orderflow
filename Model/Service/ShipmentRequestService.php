<?php

/** @noinspection PhpUndefinedClassInspection */

namespace RealtimeDespatch\OrderFlow\Model\Service;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Session\Generic;
use Psr\Log\LoggerInterface;
use RealtimeDespatch\OrderFlow\Api\Data\QuantityItemInterface;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface;
use RealtimeDespatch\OrderFlow\Api\ShipmentRequestManagementInterface;

/**
 * Class ShipmentRequestService
 *
 * @api
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ShipmentRequestService implements ShipmentRequestManagementInterface
{
    /**
     * @var Generic
     */
    protected $session;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var RequestBuilderInterface
     */
    protected $builder;

    /**
     * @var Http
     */
    protected $httpRequest;

    /**
     * Constructor
     *
     * @param Generic $session,
     * @param LoggerInterface $logger
     * @param RequestBuilderInterface $builder
     * @param Http $httpRequest
     */
    public function __construct(
        Generic $session,
        LoggerInterface $logger,
        RequestBuilderInterface $builder,
        Http $httpRequest) {
        $this->session = $session;
        $this->logger = $logger;
        $this->builder = $builder;
        $this->httpRequest = $httpRequest;
    }

    /**
     * @param string $orderIncrementId
     * @param QuantityItemInterface[] $skuQty
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
     * @api
     */
    public function create(string $orderIncrementId,
                           $skuQty = array(),
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
     * @param string $orderIncrementId
     * @param QuantityItemInterface[] $skuQty
     * @param string|null $comment
     * @param string|false $email
     * @param string|false $includeComment
     * @param string|null $courierName
     * @param string|null $serviceName
     * @param string|null $trackingNumber
     * @param string|null $dateShipped
     * @param string|null $messageSeqId
     *
     * @return void
     */
    public function _create(
        string $orderIncrementId,
        $skuQty = [],
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
        $body = [
            'orderIncrementId' => $orderIncrementId,
            'skuQtys' => $skuQty,
            'comment' => $comment,
            'email' => $email,
            'includeComment' => $includeComment,
            'courierName' => $courierName,
            'serviceName' => $serviceName,
            'trackingNumber' => $trackingNumber,
            'dateShipped' => $dateShipped,
            'sequenceId' => $messageSeqId
        ];

        $this->builder->setRequestData(
            RequestInterface::TYPE_IMPORT,
            RequestInterface::ENTITY_SHIPMENT,
            RequestInterface::OP_CREATE,
            $messageSeqId
        );

        $this->builder->setRequestBody($this->httpRequest->getContent());
        $this->builder->addRequestLine(json_encode($body), $messageSeqId);
        $this->builder->saveRequest();

        /**
         * Register request to capture response later
         *
         * see \RealtimeDespatch\OrderFlow\Plugin\Webapi\Soap\ShipmentImport
         *
         * @noinspection PhpUndefinedMethodInspection
         */
        $this->session->setRequestId($this->builder->getRequest()->getId());
    }
}
