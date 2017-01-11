<?php

namespace RealtimeDespatch\OrderFlow\Api;

/**
 * Exporter Type Interface.
 *
 * @api
 */
interface ExporterTypeInterface
{
    /**
     * Checks whether the export type is enabled
     *
     * @param integer $scopeId
     *
     * @api
     * @return boolean
     */
    public function isEnabled($scopeId = null);

    /**
     * Returns the export type
     *
     * @api
     * @return string
     */
    public function getType();

    /**
     * Exports an orderflow request.
     *
     * @api
     * @param \RealtimeDespatch\OrderFlow\Model\Request $request
     *
     * @return mixed
     */
    public function export(\RealtimeDespatch\OrderFlow\Model\Request $request);
}