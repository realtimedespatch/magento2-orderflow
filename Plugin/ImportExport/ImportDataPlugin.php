<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Plugin\ImportExport;

use Magento\CatalogImportExport\Model\Import\Product as ProductImport;
use Magento\ImportExport\Model\ResourceModel\Import\Data as ImportDataResource;
use RealtimeDespatch\OrderFlow\Model\Product\ExportStatus\ProductExportStatusResolver;

class ImportDataPlugin
{
    private const ENTITY_TYPE_CODE = 'catalog_product';

    public function __construct(
        private readonly ProductExportStatusResolver $productExportStatusResolver
    ) {}

    public function afterGetNextBunch(ImportDataResource $subject, $result)
    {
        return $this->processBunch($subject, $result);
    }

    public function afterGetNextUniqueBunch(ImportDataResource $subject, $result, $ids = null)
    {
        return $this->processBunch($subject, $result, $ids);
    }

    private function processBunch(ImportDataResource $subject, $result, $ids = null)
    {
        if (!is_array($result) || $result === []) {
            return $result;
        }

        if ($subject->getEntityTypeCode($ids) !== self::ENTITY_TYPE_CODE) {
            return $result;
        }

        $currentSku = null;
        $candidateSkus = [];
        $explicitStatusSkus = [];

        foreach ($result as $rowData) {
            if (!is_array($rowData)) {
                continue;
            }

            $rowSku = trim((string)($rowData[ProductImport::COL_SKU] ?? ''));
            if ($rowSku !== '') {
                $currentSku = $rowSku;
            }

            if ($currentSku === null) {
                continue;
            }

            $candidateSkus[$currentSku] = $currentSku;

            if (array_key_exists('orderflow_export_status', $rowData)) {
                $explicitStatusSkus[$currentSku] = $currentSku;
            }
        }

        if ($candidateSkus === []) {
            return $result;
        }

        $candidateSkus = array_values(array_diff_key($candidateSkus, $explicitStatusSkus));
        if ($candidateSkus === []) {
            return $result;
        }

        $eligibleSkus = array_flip($this->productExportStatusResolver->getSkusToSetPending($candidateSkus));
        if ($eligibleSkus === []) {
            return $result;
        }

        $currentSku = null;
        foreach ($result as $index => $rowData) {
            if (!is_array($rowData)) {
                continue;
            }

            $rowSku = trim((string)($rowData[ProductImport::COL_SKU] ?? ''));
            if ($rowSku !== '') {
                $currentSku = $rowSku;
            }

            if ($currentSku === null) {
                continue;
            }

            if (array_key_exists('orderflow_export_status', $rowData)) {
                continue;
            }

            if (!isset($eligibleSkus[$currentSku])) {
                continue;
            }

            $rowData['orderflow_export_status'] = ProductExportStatusResolver::STATUS_PENDING;
            $result[$index] = $rowData;
        }

        return $result;
    }
}
