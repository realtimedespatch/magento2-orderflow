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
     * Courier Name Getter
     *
     * @api
     * @return string
     */
    public function getCourierName();

    /**
     * Service Name Getter.
     *
     * @api
     * @return string
     */
    public function getServiceName();

    /**
     * Tracking Number Getter.
     *
     * @api
     * @return string
     */
    public function getTrackingNumber();

    /**
     * Courier Name Setter.
     *
     * @api
     * @param string $courierName
     * @return \RealtimeDespatch\OrderFlow\Api\Data\TrackItemInterface
     */
    public function setCourierName($courierName);

    /**
     * Service Name Setter.
     *
     * @api
     * @param string $serviceName
     * @return \RealtimeDespatch\OrderFlow\Api\Data\TrackItemInterface
     */
    public function setServiceName($serviceName);

    /**
     * Tracking Number Setter.
     *
     * @api
     * @param string $trackingNumber
     * @return \RealtimeDespatch\OrderFlow\Api\Data\TrackItemInterface
     */
    public function setTrackingNumber($trackingNumber);
}