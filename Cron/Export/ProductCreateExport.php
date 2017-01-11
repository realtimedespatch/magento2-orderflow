<?php

namespace RealtimeDespatch\OrderFlow\Cron\Export;

class ProductCreateExport extends \RealtimeDespatch\OrderFlow\Cron\Export\ExportCron
{
    /**
     * @var \RealtimeDespatch\OrderFlow\Helper\Export\Product
     */
    protected $_helper;

    /**
     * ProductExport constructor.
     * @param \RealtimeDespatch\OrderFlow\Helper\Export\Product $helper
     * @param \Psr\Log\LoggerInterface $logger
     * @param \RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface $requestBuilder
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Store\Model\WebsiteFactory $websiteFactory
     */
    public function __construct(
        \RealtimeDespatch\OrderFlow\Helper\Export\Product $helper,
        \Psr\Log\LoggerInterface $logger,
        \RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface $requestBuilder,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\WebsiteFactory $websiteFactory) {
        $this->_helper = $helper;
        parent::__construct($logger, $requestBuilder, $objectManager, $websiteFactory);
    }

    /**
     * Returns the request set to process.
     *
     * @param \Magento\Store\Model\Website $website
     *
     * @return array
     */
    protected function _getRequest($website)
    {
        $products = $this->_helper->getCreateableProducts($website);

        if (count($products) == 0) {
            return null;
        }

        $this->_requestBuilder->resetBuilder()->setRequestData(
            \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface::TYPE_EXPORT,
            \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface::ENTITY_PRODUCT,
            \RealtimeDespatch\OrderFlow\Api\Data\RequestInterface::OP_CREATE
        );

        $this->_requestBuilder->setScopeId($website->getId());

        foreach ($products as $product) {
            $this->_requestBuilder->addRequestLine(json_encode(array('sku' => $product->getSku())));
        }

        return $this->_requestBuilder->saveRequest();
    }

    /**
     * Returns the import entity type.
     *
     * @return \Magento\Framework\App\Helper\AbstractHelper
     */
    protected function _getHelper()
    {
        return $this->_helper;
    }
}