<?php

namespace RealtimeDespatch\OrderFlow\Api;

/**
 * @api
 */
interface ShipmentManagementInterface
{
    /**
     * Creates a new shipment
     *
     * @param array $params Shipment Params
     *
     * @return mixed
     */
    public function createShipment(array $params);
}
