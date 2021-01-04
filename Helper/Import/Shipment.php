<?php

namespace RealtimeDespatch\OrderFlow\Helper\Import;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use RealtimeDespatch\OrderFlow\Api\Data\ImportInterface;
use RealtimeDespatch\OrderFlow\Api\ImportHelperInterface;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Request\Collection;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Request\CollectionFactory as RequestCollectionFactory;

/**
 * Shipment Import Helper.
 */
class Shipment extends AbstractHelper implements ImportHelperInterface
{
    /**
     * @var RequestCollectionFactory
     */
    protected $reqCollectionFactory;

    /**
     * @param Context $context
     * @param RequestCollectionFactory $reqCollectionFactory
     */
    public function __construct(
        Context $context,
        RequestCollectionFactory $reqCollectionFactory
    ) {
        parent::__construct($context);

        $this->reqCollectionFactory = $reqCollectionFactory;
    }

    /**
     * Checks whether the import process is enabled.
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            'orderflow_shipment_import/settings/is_enabled',
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Importable Requests Getter.
     *
     * @return array
     */
    public function getImportableRequests(): array
    {
        /** @var Collection $collection */
        $collection = $this->reqCollectionFactory->create();

        return $collection->getImportableRequests(
            ImportInterface::ENTITY_SHIPMENT,
            $this->getBatchSize()
        );
    }

    /**
     * Returns the maximum batch size for processing.
     *
     * @return integer
     */
    public function getBatchSize()
    {
        return (integer) $this->scopeConfig->getValue(
            'orderflow_shipment_import/settings/batch_size',
            ScopeInterface::SCOPE_WEBSITE
        );
    }
}
