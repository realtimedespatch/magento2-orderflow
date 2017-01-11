<?php

namespace RealtimeDespatch\OrderFlow\Api;

/**
 * Import repository interface.
 *
 * @api
 */
interface ImportRepositoryInterface
{
    /**
     * Loads a specified import.
     *
     * @param int $id The import ID.
     * @return \Magento\Sales\Api\Data\ImportInterface Import interface.
     */
    public function get($id);

    /**
     * Deletes a specified import.
     *
     * @param \RealtimeDespatch\OrderFlow\Api\Data\ImportInterface $entity The import ID.
     * @return bool
     */
    public function delete(\RealtimeDespatch\OrderFlow\Api\Data\ImportInterface $entity);

    /**
     * Performs persist operations for a specified import.
     *
     * @param \RealtimeDespatch\OrderFlow\Api\Data\ImportInterface $entity The import ID.
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ImportInterface Import interface.
     */
    public function save(\RealtimeDespatch\OrderFlow\Api\Data\ImportInterface $entity);
}
