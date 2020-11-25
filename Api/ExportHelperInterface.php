<?php

namespace RealtimeDespatch\OrderFlow\Api;

interface ExportHelperInterface
{
    /**
     * Checks whether the export process is enabled.
     *
     * @param integer|null $scopeId
     *
     * @return boolean
     */
    public function isEnabled($scopeId = null);
}
