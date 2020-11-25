<?php

namespace RealtimeDespatch\OrderFlow\Model;

use Exception;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use RealtimeDespatch\OrderFlow\Api\RequestRepositoryInterface;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Request as RequestResource;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\RequestLine as RequestLineResource;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\RequestLine\CollectionFactory as RequestLineCollectionFactory;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Repository class for @see RequestInterface
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class RequestRepository implements RequestRepositoryInterface
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
     * @var RequestLineCollectionFactory
     */
    protected $requestLineCollectionFactory;

    /**
     * @param RequestResource $requestResource
     * @param RequestLineResource $requestLineResource
     * @param RequestFactory $requestFactory
     * @param RequestLineCollectionFactory $requestLineCollectionFactory
     */
    public function __construct(
        RequestResource $requestResource,
        RequestLineResource $requestLineResource,
        RequestFactory $requestFactory,
        RequestLineCollectionFactory $requestLineCollectionFactory
    ) {
        $this->requestResource = $requestResource;
        $this->requestLineResource = $requestLineResource;
        $this->requestFactory = $requestFactory;
        $this->requestLineCollectionFactory = $requestLineCollectionFactory;
    }

    /**
     * @inheritDoc
     */
    public function get(int $requestId)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $request = $this->requestFactory->create();
        $this->requestResource->load($request, $requestId);

        if (! $request->getId()) {
            throw new NoSuchEntityException(__('Request with id "%1" does not exist.', $requestId));
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $lines = $this
            ->requestLineCollectionFactory
            ->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('request_id', ['eq' => $request->getId()])
            ->loadData();

        $request->setLines($lines);

        return $request;
    }

    /**
     * @inheritDoc
     */
    public function save(RequestInterface $entity)
    {
        try {
            /* @var Request $entity */
            $this->requestResource->save($entity);

            foreach ($entity->getLines() as $requestLine) {
                $this->requestLineResource->save($requestLine);
            }
        } catch (Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function delete(RequestInterface $entity)
    {
        try {
            /* @var Request $entity */
            $this->requestResource->delete($entity);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }
}
