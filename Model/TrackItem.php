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
     * @var string
     */
    public $courierName;

    /**
     * @var string
     */
    public $serviceName;

    /**
     * TrackItem constructor.
     */
    public function __construct()
    {
        $this->trackingNumber = null;
        $this->courierName = null;
        $this->serviceName = null;
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

    /**
     * Courier Name Getter.
     *
     * @api
     * @return string
     */
    public function getCourierName()
    {
        return $this->courierName;
    }

    /**
     * Courier Name Setter.
     *
     * @api
     * @param string $courierName
     * @return \RealtimeDespatch\OrderFlow\Api\Data\TrackItemInterface
     */
    public function setCourierName($courierName) 
    {
        $this->courierName = $courierName;

        return $this;  
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
     * Service Name Setter.
     *
     * @api
     * @param string $serviceName
     * @return \RealtimeDespatch\OrderFlow\Api\Data\TrackItemInterface
     */
    public function setServiceName($serviceName) 
    {
        $this->serviceName = $serviceName;

        return $this;   
    }
}