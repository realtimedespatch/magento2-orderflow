<?php

namespace RealtimeDespatch\OrderFlow\Model;

use RealtimeDespatch\OrderFlow\Api\Data\SequenceItemInterface;

/**
 * Class SequenceItem
 *
 * @api
 * @package RealtimeDespatch\OrderFlow\Model
 */
class SequenceItem implements SequenceItemInterface
{
    /**
     * Product SKU
     *
     * @var string
     */
    private $_sku;

    /**
     * Message Sequence ID
     *
     * @var integer
     */
    private $_seq;

    /**
     * Last Exported Timestamp
     *
     * @var string
     */
    private $_lastOrderExported;

    /**
     * SequenceItem constructor.
     */
    public function __construct() {
        $this->_sku = null;
        $this->_seq = null;
        $this->_lastOrderExported = null;
    }

    /**
     * Get the sku
     *
     * @api
     * @return string The sku
     */
    public function getSku()
    {
        return $this->_sku;
    }

    /**
     * Get the sequence ID
     *
     * @api
     * @return string The sequence ID
     */
    public function getSeq()
    {
        return $this->_seq;
    }

    /**
     * Get the last order exported timestamp
     *
     * @api
     * @return string The last order exported timestamp
     */
    public function getLastOrderExported()
    {
        return $this->_lastOrderExported;
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
        $this->_sku = $sku;

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
        $this->_seq = $seq;

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
        $this->_lastOrderExported = $exported;

        return $this;
    }
}
