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
     * @param $sku string The sku
     * @return SequenceItemInterface
     * @api
     */
    public function setSku(string $sku);

    /**
     * Set the sequence ID
     *
     * @param $seq string
     * @return SequenceItemInterface
     * @api
     */
    public function setSeq(string $seq);

    /**
     * Set the last order exported timestamp
     *
     * @param $exported string
     * @return SequenceItemInterface
     * @api
     */
    public function setLastOrderExported(string $exported);
}
