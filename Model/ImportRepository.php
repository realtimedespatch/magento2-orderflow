<?php

namespace RealtimeDespatch\OrderFlow\Model;

use Exception;
use RealtimeDespatch\OrderFlow\Api\Data\ImportInterface;
use RealtimeDespatch\OrderFlow\Api\ImportRepositoryInterface;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Import as ImportResource;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\ImportLine\CollectionFactory as ImportLineCollectionFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Repository class for @see ImportInterface
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class ImportRepository implements ImportRepositoryInterface
{
    /**
     * @var ImportResource
     */
    protected $resource;

    /**
     * @var ImportFactory
     */
    protected $importFactory;

    /**
     * @var ImportLineCollectionFactory
     */
    protected $importLineCollectionFactory;

    /**
     * @param ImportResource $resource
     * @param ImportFactory $importFactory
     * @param ImportLineCollectionFactory $importLineCollectionFactory
     */
    public function __construct(
        ImportResource $resource,
        ImportFactory $importFactory,
        ImportLineCollectionFactory $importLineCollectionFactory
    ) {
        $this->resource = $resource;
        $this->importFactory = $importFactory;
        $this->importLineCollectionFactory = $importLineCollectionFactory;
    }

    /**
     * @inheritDoc
     */
    public function get(int $importId)
    {
        $import = $this->importFactory->create();
        $this->resource->load($import, $importId);

        if (! $import->getId()) {
            throw new NoSuchEntityException(__('Import with id "%1" does not exist.', $importId));
        }

        $lines = $this
            ->importLineCollectionFactory
            ->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('import_id', ['eq' => $import->getId()])
            ->loadData();

        $import->setLines($lines);

        return $import;
    }

    /**
     * @inheritDoc
     */
    public function save(ImportInterface $entity)
    {
        try {
            /* @var Import $entity */
            $this->resource->save($entity);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function delete(ImportInterface $entity)
    {
        try {
            /* @var Import $entity */
            $this->resource->delete($entity);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }
}
