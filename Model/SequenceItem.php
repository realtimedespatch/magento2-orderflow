<?php

namespace RealtimeDespatch\OrderFlow\Model;

use RealtimeDespatch\OrderFlow\Api\Data\SequenceItemInterface;

/**
 * @api
 */
class SequenceItem implements SequenceItemInterface
{
    /**
     * Product SKU
     *
     * @var string
     */
    private $sku;

    /**
     * Message Sequence ID
     *
     * @var integer
     */
    private $seq;

    /**
     * Last Exported Timestamp
     *
     * @var string
     */
    private $lastOrderExported;

    /**
     * SequenceItem constructor.
     */
    public function __construct()
    {
        $this->sku = null;
        $this->seq = null;
        $this->lastOrderExported = null;
    }

    /**
     * Get the sku
     *
     * @api
     * @return string The sku
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * Get the sequence ID
     *
     * @api
     * @return string The sequence ID
     */
    public function getSeq()
    {
        return $this->seq;
    }

    /**
     * Get the last order exported timestamp
     *
     * @api
     * @return string The last order exported timestamp
     */
    public function getLastOrderExported()
    {
        return $this->lastOrderExported;
    }

    /**
     * Set the sku
     *
     * @param $sku string The sku
     * @return SequenceItemInterface
     * @api
     */
    public function setSku(string $sku)
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * Set the sequence ID
     *
     * @param $seq string
     * @return SequenceItemInterface
     * @api
     */
    public function setSeq(string $seq)
    {
        $this->seq = $seq;

        return $this;
    }

    /**
     * Set the last order exported timestamp
     *
     * @param $exported string
     * @return SequenceItemInterface
     * @api
     */
    public function setLastOrderExported(string $exported)
    {
        $this->lastOrderExported = $exported;

        return $this;
    }
}
