<?php

namespace RealtimeDespatch\OrderFlow\Model;

use RealtimeDespatch\OrderFlow\Api\Data\TrackItemInterface;

/**
 * @api
 */
class TrackItem implements TrackItemInterface
{
    /**
     * @var string
     */
    public $trackingNumber;

    /**
     * TrackItem constructor.
     */
    public function __construct()
    {
        $this->trackingNumber = null;
    }

    /**
     * Tracking Number Getter.
     *
     * @api
     * @return string
     */
    public function getTrackingNumber()
    {
        return $this->trackingNumber;
    }

    /**
     * Tracking Number Setter.
     *
     * @param string $trackingNumber
     * @return TrackItemInterface
     * @api
     */
    public function setTrackingNumber($trackingNumber)
    {
        $this->trackingNumber = $trackingNumber;

        return $this;
    }
}