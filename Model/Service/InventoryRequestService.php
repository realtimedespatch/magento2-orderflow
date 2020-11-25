<?php

/** @noinspection PhpUndefinedClassInspection */

namespace RealtimeDespatch\OrderFlow\Model\Service;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Session\Generic;
use Psr\Log\LoggerInterface;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use RealtimeDespatch\OrderFlow\Api\InventoryRequestManagementInterface;
use RealtimeDespatch\OrderFlow\Api\Data\SequenceItemInterface;
use RealtimeDespatch\OrderFlow\Api\Data\QuantityItemInterface;
use RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface;

/**
 * Class InventoryRequestService
 *
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class InventoryRequestService implements InventoryRequestManagementInterface
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
     * @param Generic $session
     * @param LoggerInterface $logger
     * @param RequestBuilderInterface $builder
     * @param Http $httpRequest
     */
    public function __construct(
        Generic $session,
        LoggerInterface $logger,
        RequestBuilderInterface $builder,
        Http $httpRequest
    ) {
        $this->session = $session;
        $this->logger = $logger;
        $this->builder = $builder;
        $this->httpRequest = $httpRequest;
    }

    /**
     * Handles an inventory update request
     *
     * @param QuantityItemInterface[] $productQtys
     * @param SequenceItemInterface[] $productSeqs
     * @param integer $messageSeqId
     *
     * @return mixed
     * @api
     */
    public function update(array $productQtys, array $productSeqs, int $messageSeqId)
    {
        try {
           $this->_update($productQtys, $productSeqs, $messageSeqId);
        }
        catch (Exception $ex) {
            return __('Error Processing Message ').$messageSeqId;
        }

        return __('Success - Message ').json_encode($messageSeqId, true).__(' Received');
    }

    /**
     * Handles an inventory update request
     *
     * @param QuantityItemInterface[] $productQtys
     * @param SequenceItemInterface[] $productSeqs
     * @param string $messageSeqId
     *
     * @return void
     * @api
     */
    protected function _update(array $productQtys, array $productSeqs, string $messageSeqId)
    {
        $this->builder->setRequestData(
            RequestInterface::TYPE_IMPORT,
            RequestInterface::ENTITY_INVENTORY,
            RequestInterface::OP_UPDATE,
            $messageSeqId
        );

        $this->builder->setRequestBody($this->httpRequest->getContent());

        $productSeqsMap = [];
        foreach ($productSeqs as $productSeq) {
            $productSeqsMap[$productSeq->getSku()] = $productSeq;
        }

        foreach ($productQtys as $productQty) {
            $body = (array) $productQty;
            $seq = $productSeqsMap[$productQty->getSku()];
            $body['lastOrderExported'] = $seq->getLastOrderExported();
            $this->builder->addRequestLine(json_encode($body), $seq->getSeq());
        }

        $this->builder->saveRequest();

        /**
         * Register request to capture response later
         *
         * see RealtimeDespatch\OrderFlow\Plugin\Webapi\Soap\InventoryImport
         *
         * @noinspection PhpUndefinedMethodInspection
         */
        $this->session->setRequestId($this->builder->getRequest()->getId());
    }
}
