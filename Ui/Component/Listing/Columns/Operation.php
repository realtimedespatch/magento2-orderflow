<?php

namespace RealtimeDespatch\OrderFlow\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;
use RealtimeDespatch\OrderFlow\Model\Source\OperationSource;

class Operation extends Column
{
    /**
     * Switches the 'Create' and 'Update' Operation Labels to 'Queue'.
     *
     * This is easier for the end user to interpret as these operation types essentially queue an order, or prod
     * to be picked up from Magento by OrderFlow.
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['operation']) && $item['operation'] == OperationSource::OPERATION_CREATE) {
                    $item[$this->getData('name')] = OperationSource::OPERATION_QUEUE;
                } elseif (isset($item['operation']) && $item['operation'] == OperationSource::OPERATION_UPDATE) {
                    $item[$this->getData('name')] = OperationSource::OPERATION_QUEUE;
                }
            }
        }

        return $dataSource;
    }
}
