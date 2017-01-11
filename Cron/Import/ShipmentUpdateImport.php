<?php

namespace RealtimeDespatch\OrderFlow\Cron\Import;

class ShipmentUpdateImport extends \RealtimeDespatch\OrderFlow\Cron\Import\ImportCron
{
    const ENTITY_SHIPMENT = 'Shipment';

    /**
     * @var \RealtimeDespatch\OrderFlow\Helper\Import\Shipment
     */
    protected $_helper;

    /**
     * ShipmentImport constructor.
     * @param \RealtimeDespatch\OrderFlow\Helper\Import\Shipment $helper
     * @param \Psr\Log\LoggerInterface $logger
     * @param \RealtimeDespatch\OrderFlow\Model\RequestFactory $requestFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \RealtimeDespatch\OrderFlow\Helper\Import\Shipment $helper,
        \Psr\Log\LoggerInterface $logger,
        \RealtimeDespatch\OrderFlow\Model\RequestFactory $requestFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager) {
        $this->_helper = $helper;
        parent::__construct($logger, $requestFactory, $objectManager);
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

    /**
     * Returns the import entity type.
     *
     * @return string
     */
    protected function _getEntityType()
    {
        return self::ENTITY_SHIPMENT;
    }
}