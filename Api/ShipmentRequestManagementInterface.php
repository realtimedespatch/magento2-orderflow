<?php

namespace RealtimeDespatch\OrderFlow\Api;

use RealtimeDespatch\OrderFlow\Api\Data\QuantityItemInterface;

/**
 * Shipment Request Management Interface.
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @api
 */
interface ShipmentRequestManagementInterface
{
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
                           $messageSeqId = null);
}
