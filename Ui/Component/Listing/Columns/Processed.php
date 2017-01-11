<?php

namespace RealtimeDespatch\OrderFlow\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Date;

class Processed extends Date
{
    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[$this->getData('name')])) {
                    $date = $this->timezone->date(new \DateTime($item[$this->getData('name')]));
                    if (isset($this->getConfiguration()['timezone']) && !$this->getConfiguration()['timezone']) {
                        $date = new \DateTime($item[$this->getData('name')]);
                    }
                    $item[$this->getData('name')] = $date->format('Y-m-d H:i:s');
                } else {
                    $item[$this->getData('name')] = __('Pending');
                }
            }
        }

        return $dataSource;
    }
}