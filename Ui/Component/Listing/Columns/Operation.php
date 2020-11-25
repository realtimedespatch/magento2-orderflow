<?php

namespace RealtimeDespatch\OrderFlow\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;

class Operation extends Column
{
    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['operation']) && $item['operation'] == 'Create') {
                    $item[$this->getData('name')] = 'Queue';
                } else if (isset($item['operation']) && $item['operation'] == 'Update') {
                    $item[$this->getData('name')] = 'Queue';
                }
            }
        }

        return $dataSource;
    }
}
