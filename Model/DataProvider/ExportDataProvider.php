<?php

namespace RealtimeDespatch\OrderFlow\Model\DataProvider;

use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use Magento\Framework\Api\Search\SearchResultInterface;

/**
 * @noinspection DuplicatedCode
 */
class ExportDataProvider extends DataProvider
{
    /**
     * @param SearchResultInterface $searchResult
     * @return array
     */
    protected function searchResultToOutput(SearchResultInterface $searchResult)
    {
        $arrItems = [];

        $arrItems['items'] = [];
        foreach ($searchResult->getItems() as $exportItem) {
            // We need to remove the blob data as it breaks the frontend grid
            /** @noinspection PhpUndefinedMethodInspection */
            $exportItem->setRequestBody(null);
            /** @noinspection PhpUndefinedMethodInspection */
            $exportItem->setResponseBody(null);

            $itemData = [];
            foreach ($exportItem->getCustomAttributes() as $attribute) {
                $itemData[$attribute->getAttributeCode()] = $attribute->getValue();
            }
            $arrItems['items'][] = $itemData;
        }

        $arrItems['totalRecords'] = $searchResult->getTotalCount();

        return $arrItems;
    }
}
