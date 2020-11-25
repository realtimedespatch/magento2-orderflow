<?php

namespace RealtimeDespatch\OrderFlow\Model\Import;

use Magento\Ui\DataProvider\AbstractDataProvider;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Import\CollectionFactory;

class DataProvider extends AbstractDataProvider
{
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data
        );

        $this->collection = $collectionFactory->create();
    }

    public function getData()
    {
        return [];
    }
}
