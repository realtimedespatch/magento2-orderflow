<?php

namespace RealtimeDespatch\OrderFlow\Api\Data;

/**
 * Track Item Interface
 *
 * @api
 */
interface TrackItemInterface
{
    /**
     * Tracking Number Getter.
     *
     * @api
     * @return string
     */
    public function getTrackingNumber();

    /**
     * Tracking Number Setter.
     *
     * @api
     * @param string $trackingNumber
     * @return \RealtimeDespatch\OrderFlow\Api\Data\TrackItemInterface
     */
    public function setTrackingNumber($trackingNumber);
}