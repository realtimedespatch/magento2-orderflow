<?php

namespace RealtimeDespatch\OrderFlow\Model\Service;

use RealtimeDespatch\OrderFlow\Api\InventoryRequestManagementInterface;
use RealtimeDespatch\OrderFlow\Api\Data\SequenceItemInterface;
use RealtimeDespatch\OrderFlow\Api\Data\QuantityItemInterface;

/**
 * Class InventoryRequestService
 *
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class InventoryRequestService implements InventoryRequestManagementInterface
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
     * Handles an inventory update request
     *
     * @api
     * @param QuantityItemInterface[] $productQtys
     * @param SequenceItemInterface[] $productSeqs
     * @param integer $messageSeqId
     *
     * @return mixed
     */
    public function update($productQtys, $productSeqs, $messageSeqId)
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
     * @api
     * @param QuantityItemInterface[] $productQtys
     * @param SequenceItemInterface[] $productSeqs
     * @param integer $messageSeqId
     *
     * @return void
     */
    protected function _update($productQtys, $productSeqs, $messageSeqId)
    {
        $this->_builder->setRequestData(
            \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface::TYPE_IMPORT,
            \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface::ENTITY_INVENTORY,
            \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface::OP_UPDATE,
            $messageSeqId
        );

        $this->_builder->setRequestBody($this->_httpRequest->getContent());

        $productSeqsMap = [];
        foreach ($productSeqs as $productSeq) {
            $productSeqsMap[$productSeq->getSku()] = $productSeq;
        }

        foreach ($productQtys as $productQty) {
            $body = (array) $productQty;
            $seq = $productSeqsMap[$productQty->getSku()];
            $body['lastOrderExported'] = $seq->getLastOrderExported();
            $this->_builder->addRequestLine(json_encode($body), $seq->getSeq());
        }

        $this->_builder->saveRequest();

        // Register request to capture response later.
        $this->_registry->register(
            'request_id',
            $this->_builder->getRequest()->getId()
        );
    }
}