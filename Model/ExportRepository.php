<?php

namespace RealtimeDespatch\OrderFlow\Model;

use Magento\Cms\Api\Data\ExportInterface;

use RealtimeDespatch\OrderFlow\Api\ExportRepositoryInterface;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Export as ExportResource;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Repository class for @see ExportInterface
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ExportRepository implements \RealtimeDespatch\OrderFlow\Api\ExportRepositoryInterface
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
     * @var ExportLineFactory
     */
    protected $exportLineFactory;

    /**
     * @param ExportResource $resource
     * @param ExportFactory $exportFactory
     * @param RealtimeDespatch\OrderFlow\Model\ResourceModel\ExportLine\CollectionFactory
     */
    public function __construct(
        ExportResource $resource,
        ExportFactory $exportFactory,
        ExportLineFactory $exportLineFactory
    ) {
        $this->resource = $resource;
        $this->exportFactory = $exportFactory;
        $this->exportLineFactory = $exportLineFactory;
    }

    /**
     * Load export data by given export ID.
     *
     * @param string $exportId
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($exportId)
    {
        $export = $this->exportFactory->create();
        $this->resource->load($export, $exportId);

        if ( ! $export->getId()) {
            throw new NoSuchEntityException(__('Export with id "%1" does not exist.', $exportId));
        }

        $lines = $this->exportLineFactory
            ->create()
            ->getCollection()
            -> addFieldToSelect('*')
            ->addFieldToFilter('export_id', ['eq' => $export->getId()])
            ->loadData();

        $export->setLines($lines);

        return $export;
    }

    /**
     * Save Export
     *
     * @param \RealtimeDespatch\OrderFlow\Api\Data\ExportInterface $export
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportInterface
     * @throws CouldNotSaveException
     */
    public function save(\RealtimeDespatch\OrderFlow\Api\Data\ExportInterface $entity)
    {
        try {
            $this->resource->save($entity);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $entity;
    }

    /**
     * Delete Export
     *
     * @param \RealtimeDespatch\OrderFlow\Api\Data\ExportInterface $export
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(\RealtimeDespatch\OrderFlow\Api\Data\ExportInterface $entity)
    {
        try {
            $this->resource->delete($entity);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }
}