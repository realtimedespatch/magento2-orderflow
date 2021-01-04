<?php

namespace RealtimeDespatch\OrderFlow\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;

class UnitsReceived extends Column
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
                    $item['units_received'] = isset($data['unitsReceived']) ? $data['unitsReceived'] : 0;
                }
            }
        }

        return $dataSource;
    }
}
