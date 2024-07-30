<?php

namespace RealtimeDespatch\OrderFlow\Model;

use Magento\Cms\Api\Data\RequestInterface;

use RealtimeDespatch\OrderFlow\Api\RequestRepositoryInterface;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Request as RequestResource;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\RequestLine as RequestLineResource;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Repository class for @see RequestInterface
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RequestRepository implements \RealtimeDespatch\OrderFlow\Api\RequestRepositoryInterface
{
    /**
     * @var RequestResource
     */
    protected $requestResource;

    /**
     * @var RequestLineResource
     */
    protected $requestLineResource;

    /**
     * @var RequestFactory
     */
    protected $requestFactory;

    /**
     * @var RequestLineFactory
     */
    protected $requestLineFactory;

    /**
     * @param RequestResource $requestResoure
     * @param RequestLineResource $requestLineResource
     * @param RequestFactory $requestFactory
     * @param RealtimeDespatch\OrderFlow\Model\ResourceModel\RequestLine\CollectionFactory
     */
    public function __construct(
        RequestResource $requestResource,
        RequestLineResource $requestLineResource,
        RequestFactory $requestFactory,
        RequestLineFactory $requestLineFactory
    ) {
        $this->requestResource = $requestResource;
        $this->requestLineResource = $requestLineResource;
        $this->requestFactory = $requestFactory;
        $this->requestLineFactory = $requestLineFactory;
    }

    /**
     * Load request data by given request ID.
     *
     * @param string $requestId
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($requestId)
    {
        $request = $this->requestFactory->create();
        $this->requestResource->load($request, $requestId);

        if ( ! $request->getId()) {
            throw new NoSuchEntityException(__('Request with id "%1" does not exist.', $requestId));
        }

        $lines = $this->requestLineFactory
            ->create()
            ->getCollection()
            -> addFieldToSelect('*')
            ->addFieldToFilter('request_id', ['eq' => $request->getId()])
            ->loadData();

        $request->setLines($lines);

        return $request;
    }

    /**
     * Save Request
     *
     * @param \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface $request
     * @return \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface
     * @throws CouldNotSaveException
     */
    public function save(\RealtimeDespatch\OrderFlow\Api\Data\RequestInterface $entity)
    {
        try {
            $this->requestResource->save($entity);

            foreach ($entity->getLines() as $requestLine) {
                $this->requestLineResource->save($requestLine);
            }
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $entity;
    }

    /**
     * Delete Request
     *
     * @param \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface $request
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(\RealtimeDespatch\OrderFlow\Api\Data\RequestInterface $entity)
    {
        try {
            $this->requestResource->delete($entity);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }
}