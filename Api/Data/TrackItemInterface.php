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

    /**
     * Courier Name Getter.
     *
     * @api
     * @return string
     */
    public function getCourierName();

    /**
     * Courier Name Setter.
     *
     * @api
     * @param string $courierName
     * @return \RealtimeDespatch\OrderFlow\Api\Data\TrackItemInterface
     */
    public function setCourierName($courierName);

    /**
     * Service Name Getter.
     *
     * @api
     * @return string
     */
    public function getServiceName();

    /**
     * Service Name Setter.
     *
     * @api
     * @param string $serviceName
     * @return \RealtimeDespatch\OrderFlow\Api\Data\TrackItemInterface
     */
    public function setServiceName($serviceName);
}