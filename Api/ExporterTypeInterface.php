<?php

namespace RealtimeDespatch\OrderFlow\Api;

use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;

/**
 * Exporter Type Interface.
 *
 * Defines the methods available for an export processor type.
 *
 * @api
 */
interface ExporterTypeInterface
{
    /**
     * Checks whether the export type is enabled
     *
     * @param null|integer $scopeId
     *
     * @return boolean
     * @api
     */
    public function isEnabled($scopeId = null);

    /**
     * Export Type Getter.
     *
     * @api
     * @return string
     */
    public function getType();

    /**
     * Processes an Export Request.
     *
     * @api
     * @param RequestInterface $request
     *
     * @return mixed
     */
    public function export(RequestInterface $request);
}
