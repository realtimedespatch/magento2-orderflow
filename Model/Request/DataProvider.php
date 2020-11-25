<?php

namespace RealtimeDespatch\OrderFlow\Model\Request;

use Magento\Ui\DataProvider\AbstractDataProvider;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Request\CollectionFactory;

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

        /** @noinspection PhpUndefinedMethodInspection */
        $this->collection = $collectionFactory->create();
    }

    public function getData()
    {
        return [];
    }
}
