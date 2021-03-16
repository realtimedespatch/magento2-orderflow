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
    public $courierName;

    /**
     * @var string
     */
    public $serviceName;

    /**
     * @var string
     */
    public $trackingNumber;

    /**
     * TrackItem constructor.
     */
    public function __construct()
    {
        $this->courierName = null;
        $this->serviceName = null;
        $this->trackingNumber = null;
    }

    /**
     * Courier Name Getter
     *
     * @api
     * @return string
     */
    public function getCourierName()
    {
        return $this->courierName;
    }

    /**
     * Service Name Getter.
     *
     * @api
     * @return string
     */
    public function getServiceName()
    {
        return $this->serviceName;
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
     * Courier Name Setter.
     *
     * @param string $courierName
     * @return TrackItemInterface
     * @api
     */
    public function setCourierName($courierName)
    {
        $this->courierName = $courierName;

        return $this;
    }

    /**
     * Service Name Setter.
     *
     * @param string $serviceName
     * @return TrackItemInterface
     * @api
     */
    public function setServiceName($serviceName)
    {
        $this->serviceName = $serviceName;

        return $this;
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