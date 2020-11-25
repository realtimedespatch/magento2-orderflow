<?php

namespace RealtimeDespatch\OrderFlow\Api;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use RealtimeDespatch\OrderFlow\Api\Data\ExportInterface;

/**
 * @api
 */
interface ExportRepositoryInterface
{
    /**
     * Loads a specified export.
     *
     * @param int $exportId The export ID.
     * @return ExportInterface Export interface.
     * @throws NoSuchEntityException
     */
    public function get(int $exportId);

    /**
     * Deletes a specified export.
     *
     * @param ExportInterface $entity The export ID.
     * @return bool
     */
    public function delete(ExportInterface $entity);

    /**
     * Performs persist operations for a specified export.
     *
     * @param ExportInterface $entity The export ID.
     * @return ExportInterface Export interface.
     * @throws CouldNotSaveException
     */
    public function save(ExportInterface $entity);
}
