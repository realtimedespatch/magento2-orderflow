<?php

namespace RealtimeDespatch\OrderFlow\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Date;
use RealtimeDespatch\OrderFlow\Model\Source\Export\Status as ExportStatus;

class Exported extends Date
{
    /**
     * Sets the exported column value to Pending for entities where this value is null.
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (! isset($item[$this->getData('name')])) {
                    $item[$this->getData('name')] = ExportStatus::STATUS_PENDING;
                    $item[$this->getData('name')] = __($item[$this->getData('name')]);
                }
            }
        }

        return $dataSource;
    }
}
