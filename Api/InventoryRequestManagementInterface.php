<?php

namespace RealtimeDespatch\OrderFlow\Api;

/**
 * Inventory Request Management Interface.
 *
 * @api
 */
interface InventoryRequestManagementInterface
{
    /**
     * @api
     * @param RealtimeDespatch\OrderFlow\Api\Data\QuantityItemInterface[] $productQtys
     * @param RealtimeDespatch\OrderFlow\Api\Data\SequenceItemInterface[] $productSeqs
     * @param integer                                                     $messageSeqId
     *
     * @return mixed
     */
    public function update($productQtys, $productSeqs, $messageSeqId);
}