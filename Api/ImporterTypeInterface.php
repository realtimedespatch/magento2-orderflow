<?php

namespace RealtimeDespatch\OrderFlow\Api;

use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;

/**
 * Importer Type Interface.
 *
 * Defines the methods available for an importer type.
 *
 * @api
 */
interface ImporterTypeInterface
{
    /**
     * Enabled Getter.
     *
     * @api
     * @return boolean
     */
    public function isEnabled();

    /**
     * Importer Type Getter.
     *
     * @api
     * @return string
     */
    public function getType();

    /**
     * Process Import Request.
     *
     * @api
     * @param RequestInterface $request $request
     *
     * @return mixed
     */
    public function import(RequestInterface $request);
}
