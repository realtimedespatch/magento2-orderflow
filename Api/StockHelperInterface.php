<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Api;

interface StockHelperInterface
{
    public function updateProductStock($sku, $qty, $lastOrderExported, $source = "default");
    public function calculateProductStock($productId, $unitsReceived, $lastOrderExported);
}