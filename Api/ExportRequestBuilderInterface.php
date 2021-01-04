<?php

namespace RealtimeDespatch\OrderFlow\Api;

use Magento\Store\Model\Website;

interface ExportRequestBuilderInterface
{
    /**
     * Builds new requests for products are available for creation and update.
     *
     * @param Website $website
     */
    public function build(Website $website);
}
