<?php

namespace RealtimeDespatch\OrderFlow\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Date;

class ProductExported extends Date
{
    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if ( ! isset($item[$this->getData('name')])) {
                    $item[$this->getData('name')] = __('Pending');
                }
            }
        }

        return $dataSource;
    }
}