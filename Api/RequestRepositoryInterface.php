<?php

namespace RealtimeDespatch\OrderFlow\Api;

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
     * @param int $id The request ID.
     * @return \Magento\Sales\Api\Data\RequestInterface Request interface.
     */
    public function get($id);

    /**
     * Deletes a specified request.
     *
     * @param \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface $entity The request ID.
     * @return bool
     */
    public function delete(\RealtimeDespatch\OrderFlow\Api\Data\RequestInterface $entity);

    /**
     * Performs persist operations for a specified request.
     *
     * @param \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface $entity The request ID.
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface Request interface.
     */
    public function save(\RealtimeDespatch\OrderFlow\Api\Data\RequestInterface $entity);
}
