<?php

namespace RealtimeDespatch\OrderFlow\Api;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;

/**
 * Request repository interface.
 *
 * @api
 */
interface RequestRepositoryInterface
{
    /**
     * Loads a specified request.
     *
     * @param int $requestId The request ID.
     * @return RequestInterface Request interface.
     * @throws NoSuchEntityException
     */
    public function get(int $requestId);

    /**
     * Performs persist operations for a specified request.
     *
     * @param RequestInterface $entity The request ID.
     * @return RequestInterface Request interface.
     * @throws CouldNotSaveException
     */
    public function save(RequestInterface $entity);

    /**
     * Deletes a specified request.
     *
     * @param RequestInterface $entity The request ID.
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(RequestInterface $entity);
}
