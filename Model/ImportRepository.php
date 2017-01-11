<?php

namespace RealtimeDespatch\OrderFlow\Model;

use Magento\Cms\Api\Data\ImportInterface;

use RealtimeDespatch\OrderFlow\Api\ImportRepositoryInterface;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Import as ImportResource;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Repository class for @see ImportInterface
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ImportRepository implements \RealtimeDespatch\OrderFlow\Api\ImportRepositoryInterface
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
     * @var ImportLineFactory
     */
    protected $importLineFactory;

    /**
     * @param ImportResource $resource
     * @param ImportFactory $importFactory
     * @param RealtimeDespatch\OrderFlow\Model\ResourceModel\ImportLine\CollectionFactory
     */
    public function __construct(
        ImportResource $resource,
        ImportFactory $importFactory,
        ImportLineFactory $importLineFactory
    ) {
        $this->resource = $resource;
        $this->importFactory = $importFactory;
        $this->importLineFactory = $importLineFactory;
    }

    /**
     * Load import data by given import ID.
     *
     * @param string $importId
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ImportInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($importId)
    {
        $import = $this->importFactory->create();
        $this->resource->load($import, $importId);

        if ( ! $import->getId()) {
            throw new NoSuchEntityException(__('Import with id "%1" does not exist.', $importId));
        }

        $lines = $this->importLineFactory
            ->create()
            ->getCollection()
            -> addFieldToSelect('*')
            ->addFieldToFilter('import_id', ['eq' => $import->getId()])
            ->loadData();

        $import->setLines($lines);

        return $import;
    }

    /**
     * Save Import
     *
     * @param \RealtimeDespatch\OrderFlow\Api\Data\ImportInterface $import
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ImportInterface
     * @throws CouldNotSaveException
     */
    public function save(\RealtimeDespatch\OrderFlow\Api\Data\ImportInterface $entity)
    {
        try {
            $this->resource->save($import);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $import;
    }

    /**
     * Delete Import
     *
     * @param \RealtimeDespatch\OrderFlow\Api\Data\ImportInterface $import
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(\RealtimeDespatch\OrderFlow\Api\Data\ImportInterface $entity)
    {
        try {
            $this->resource->delete($import);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }
}