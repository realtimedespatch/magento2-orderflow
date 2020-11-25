<?php

namespace RealtimeDespatch\OrderFlow\Model\DataProvider;

use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use Magento\Framework\Api\Search\SearchResultInterface;

/**
 * @noinspection DuplicatedCode
 */
class ImportDataProvider extends DataProvider
{
    /**
     * @param SearchResultInterface $searchResult
     * @return array
     */
    protected function searchResultToOutput(SearchResultInterface $searchResult)
    {
        $arrItems = [];

        $arrItems['items'] = [];
        foreach ($searchResult->getItems() as $importItem) {
            // We need to remove the blob data as it breaks the frontend grid
            /** @noinspection PhpUndefinedMethodInspection */
            $importItem->setRequestBody(null);
            /** @noinspection PhpUndefinedMethodInspection */
            $importItem->setResponseBody(null);

            $itemData = [];
            foreach ($importItem->getCustomAttributes() as $attribute) {
                $itemData[$attribute->getAttributeCode()] = $attribute->getValue();
            }
            $arrItems['items'][] = $itemData;
        }

        $arrItems['totalRecords'] = $searchResult->getTotalCount();

        return $arrItems;
    }
}
