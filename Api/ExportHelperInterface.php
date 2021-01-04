<?php

namespace RealtimeDespatch\OrderFlow\Api;

use RealtimeDespatch\OrderFlow\Model\ResourceModel\Request\Collection;

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

    /**
     * Exportable Requests Getter.
     *
     * @param integer|null $scopeId
     *
     * @return Collection
     */
    public function getExportableRequests($scopeId = null);
}
