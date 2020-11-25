<?php

namespace RealtimeDespatch\OrderFlow\Api;

interface ImportHelperInterface
{
    /**
     * Checks whether the import process is enabled.
     *
     * @return boolean
     */
    public function isEnabled();
}
