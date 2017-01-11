<?php

namespace RealtimeDespatch\OrderFlow\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;

class UnitsCalculated extends Column
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
                    $item['units_calculated'] = isset($data['unitsCalculated']) ? $data['unitsCalculated'] : 0;
                }
            }
        }

        return $dataSource;
    }
}