<?php

namespace RealtimeDespatch\OrderFlow\Api\Data;

/**
 * Sequence Item Interface
 *
 * @api
 */
interface SequenceItemInterface
{
    /**
     * Get the sku
     *
     * @api
     * @return string The sku
     */
    public function getSku();

    /**
     * Get the sequence ID
     *
     * @api
     * @return string The sequence ID
     */
    public function getSeq();

    /**
     * Get the last order exported timestamp
     *
     * @api
     * @return string The last order exported timestamp
     */
    public function getLastOrderExported();

    /**
     * Set the sku
     *
     * @api
     * @param $sku string The sku
     * @return \RealtimeDespatch\OrderFlow\Api\Data\SequenceItemInterface
     */
    public function setSku($sku);

    /**
     * Set the sequence ID
     *
     * @api
     * @param $seq string
     * @return \RealtimeDespatch\OrderFlow\Api\Data\SequenceItemInterface
     */
    public function setSeq($seq);

    /**
     * Set the last order exported timestamp
     *
     * @api
     * @param $exported string
     * @return \RealtimeDespatch\OrderFlow\Api\Data\SequenceItemInterface
     */
    public function setLastOrderExported($exported);
}