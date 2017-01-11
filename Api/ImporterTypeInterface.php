<?php

namespace RealtimeDespatch\OrderFlow\Api;

/**
 * Importer Type Interface.
 *
 * @api
 */
interface ImporterTypeInterface
{
    /**
     * Checks whether the import type is enabled
     *
     * @api
     * @return boolean
     */
    public function isEnabled();

    /**
     * Returns the import type
     *
     * @api
     * @return string
     */
    public function getType();

    /**
     * Imports an orderflow request.
     *
     * @api
     * @param \RealtimeDespatch\OrderFlow\Model\Request $request
     *
     * @return mixed
     */
    public function import(\RealtimeDespatch\OrderFlow\Model\Request $request);
}