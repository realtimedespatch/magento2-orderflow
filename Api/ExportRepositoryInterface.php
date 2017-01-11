<?php

namespace RealtimeDespatch\OrderFlow\Api;

/**
 * Export repository interface.
 *
 * @api
 */
interface ExportRepositoryInterface
{
    /**
     * Loads a specified export.
     *
     * @param int $id The export ID.
     * @return \Magento\Sales\Api\Data\ExportInterface Export interface.
     */
    public function get($id);

    /**
     * Deletes a specified export.
     *
     * @param \RealtimeDespatch\OrderFlow\Api\Data\ExportInterface $entity The export ID.
     * @return bool
     */
    public function delete(\RealtimeDespatch\OrderFlow\Api\Data\ExportInterface $entity);

    /**
     * Performs persist operations for a specified export.
     *
     * @param \RealtimeDespatch\OrderFlow\Api\Data\ExportInterface $entity The export ID.
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportInterface Export interface.
     */
    public function save(\RealtimeDespatch\OrderFlow\Api\Data\ExportInterface $entity);
}
