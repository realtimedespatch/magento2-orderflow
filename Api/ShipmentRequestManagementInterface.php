<?php

namespace RealtimeDespatch\OrderFlow\Api;

/**
 * Shipment Request Management Interface.
 *
 * @api
 */
interface ShipmentRequestManagementInterface
{
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
                           $messageSeqId = null);
}