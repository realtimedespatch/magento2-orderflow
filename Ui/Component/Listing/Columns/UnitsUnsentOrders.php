<?php

namespace RealtimeDespatch\OrderFlow\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;

class UnitsUnsentOrders extends Column
{
    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['additional_data'])) {
                    $data = json_decode($item['additional_data'], true);
                    $item['unsent_orders'] = isset($data['unitsUnsentOrders']) ? $data['unitsUnsentOrders'] : 0;
                }
            }
        }

        return $dataSource;
    }
}