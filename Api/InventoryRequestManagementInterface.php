<?php

namespace RealtimeDespatch\OrderFlow\Api;

use RealtimeDespatch\OrderFlow\Api\Data\QuantityItemInterface;
use RealtimeDespatch\OrderFlow\Api\Data\SequenceItemInterface;

/**
 * Inventory Request Management Interface.
 *
 * @api
 */
interface InventoryRequestManagementInterface
{
    /**
     * @param QuantityItemInterface[] $productQtys
     * @param SequenceItemInterface[] $productSeqs
     * @param integer $messageSeqId
     *
     * @return mixed
     * @api
     */
    public function update(array $productQtys, array $productSeqs, int $messageSeqId);
}
