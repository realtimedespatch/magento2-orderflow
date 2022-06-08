<?php

namespace RealtimeDespatch\OrderFlow\Api;

/**
 * Shipment Management Interface.
 *
 * @api
 */
interface ShipmentManagementInterface
{
    /**
     * Creates new shipment(s)
     *
     * @param array $params Shipment Params
     *
     * @return mixed
     */
    public function createShipments($params);
}
