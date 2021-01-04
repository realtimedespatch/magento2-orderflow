<?php

namespace RealtimeDespatch\OrderFlow\Api;

use RealtimeDespatch\OrderFlow\Api\Data\QuantityItemInterface;

/**
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
    public function create(
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
    );
}
