<?php

namespace RealtimeDespatch\OrderFlow\Api;

use RealtimeDespatch\OrderFlow\Model\ResourceModel\Request\Collection;

interface ImportHelperInterface
{
    /**
     * Checks whether the import process is enabled.
     *
     * @return boolean
     */
    public function isEnabled();

    /**
     * Importable Requests Getter.
     *
     * @return Collection
     */
    public function getImportableRequests();
}
