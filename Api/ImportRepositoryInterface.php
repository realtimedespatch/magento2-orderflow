<?php

namespace RealtimeDespatch\OrderFlow\Api;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use RealtimeDespatch\OrderFlow\Api\Data\ImportInterface;

/**
 * Import Repository Interface.
 *
 * Method definitions for the import repository.
 *
 * @api
 */
interface ImportRepositoryInterface
{
    /**
     * Loads a specified import.
     *
     * @param int $importId
     * @return ImportInterface
     * @throws NoSuchEntityException
     */
    public function get(int $importId);

    /**
     * Deletes a specified import.
     *
     * @param ImportInterface $entity The import ID.
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(ImportInterface $entity);

    /**
     * Performs persist operations for a specified import.
     *
     * @param ImportInterface $entity The import ID.
     * @return ImportInterface Import interface.
     * @throws CouldNotSaveException
     */
    public function save(ImportInterface $entity);
}
