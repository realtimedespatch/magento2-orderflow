<?php

namespace RealtimeDespatch\OrderFlow\Model;

use Exception;
use RealtimeDespatch\OrderFlow\Api\Data\ExportInterface;
use RealtimeDespatch\OrderFlow\Api\ExportRepositoryInterface;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Export as ExportResource;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\ExportLine\CollectionFactory as ExportLineCollectionFactory;

/**
 * Repository class for @see ExportInterface
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ExportRepository implements ExportRepositoryInterface
{
    /**
     * @var ExportResource
     */
    protected $resource;

    /**
     * @var ExportFactory
     */
    protected $exportFactory;

    /**
     * @var ExportLineCollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param ExportResource $resource
     * @param ExportFactory $exportFactory
     * @param ExportLineCollectionFactory $collectionFactory
     */
    public function __construct(
        ExportResource $resource,
        ExportFactory $exportFactory,
        ExportLineCollectionFactory $collectionFactory
    ) {
        $this->resource = $resource;
        $this->exportFactory = $exportFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @inheritDoc
     */
    public function get(int $exportId)
    {
        $export = $this->exportFactory->create();
        $this->resource->load($export, $exportId);

        if (! $export->getId()) {
            throw new NoSuchEntityException(__('Export with id "%1" does not exist.', $exportId));
        }

        $lines = $this
            ->collectionFactory
            ->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('export_id', ['eq' => $export->getId()])
            ->loadData();

        $export->setLines($lines);

        return $export;
    }

    /**
     * @inheritDoc
     * @throws CouldNotSaveException
     */
    public function save(ExportInterface $entity)
    {
        try {
            /* @var Export $entity */
            $this->resource->save($entity);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $entity;
    }

    /**
     * @inheritDoc
     * @throws CouldNotDeleteException
     */
    public function delete(ExportInterface $entity)
    {
        try {
            /* @var Export $entity */
            $this->resource->delete($entity);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }
}
