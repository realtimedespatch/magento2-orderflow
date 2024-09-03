<?php

namespace RealtimeDespatch\OrderFlow\Model\Service;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\Order\Shipment\Track;
use RealtimeDespatch\OrderFlow\Api\Data\TrackItemInterface;
use RealtimeDespatch\OrderFlow\Api\ShipmentManagementInterface;

/**
 * Class ShipmentService
 *
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ShipmentService implements ShipmentManagementInterface
{
    /**
     * @var RealtimeDespatch\OrderFlow\Helper\Import\Shipment
     */
    protected $_helper;

    /**
     * @param Magento\Sales\Model\OrderFactory $orderFactory
     */
    protected $_orderFactory;

    /**
     * @param Magento\Shipping\Model\Order\TrackFactory $trackFactory
     */
    protected $_trackFactory;

    /**
     * @param Magento\Sales\Model\Convert\Order $orderConverter
     */
    protected $_orderConverter;

    /**
     * @param Magento\Shipping\Model\ShipmentNotifier $shipmentNotifier
     */
    protected $_shipmentNotifier;

    /**
     * @var \Magento\Framework\Module\Manager $_moduleManager
     */
    protected $_moduleManager;

    /**
     * @param Psr\Log\LoggerInterface $logger
     * @param Magento\Framework\Event\ManagerInterface $eventManager
     * @param RealtimeDespatch\OrderFlow\Helper\Import\Shipment $helper
     * @param Magento\Sales\Model\OrderRepository $orderRepository
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \RealtimeDespatch\OrderFlow\Helper\Import\Shipment $helper,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Shipping\Model\Order\TrackFactory $trackFactory,
        \Magento\Sales\Model\Convert\Order $orderConverter,
        \Magento\Shipping\Model\ShipmentNotifier $shipmentNotifier,
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->_helper = $helper;
        $this->_orderFactory = $orderFactory;
        $this->_trackFactory = $trackFactory;
        $this->_orderConverter = $orderConverter;
        $this->_shipmentNotifier = $shipmentNotifier;
        $this->_moduleManager = $moduleManager;
    }

    /**
     * Creates a new shipment
     *
     * @param object $params Shipment Params
     *
     * @return mixed
     */
    public function createShipments($params)
    {
        try {

            // divide sku qtys by source
            $sourceSkuQtys = [];
            foreach ($params->skuQtys as $skuQty) {
                $source = $skuQty->source;
                if (!isset($sourceSkuQtys[$source])) {
                    $sourceSkuQtys[$source] = [];
                }
                $sourceSkuQtys[$source][] = $skuQty;
            }

            foreach ($sourceSkuQtys as $source => $skuQtys) {
                // Create shipment.
                $shipment = $this->_createShipment($params);
                if ($this->_moduleManager->isEnabled('Magento_InventoryShipping')) {
                    $shipment->getExtensionAttributes()->setSourceCode($source);
                }

                // Add line(s).
                $this->_createShipmentLines($shipment, $skuQtys);

                // Add tracks.
                $this->_addTracks($shipment, $params);

                // Register shipment.
                $shipment->register();
                $shipment->getOrder()->setIsInProcess(true);

                // Ssve shipment.
                $shipment->save();
                $shipment->getOrder()->save();

                // Notify customer.
                if ($params->email) {
                    $this->_shipmentNotifier->notify($shipment);
                }

                $shipment->save();
            }
        } catch (\Exception $e) {
            throw new LocalizedException(
                __($e->getMessage())
            );
        }
    }

    /**
     * Creates a new shipment
     *
     * @param array $params Shipment params.
     *
     * @return Shipment
     * @throws LocalizedException
     */
    protected function _createShipment($params)
    {
        $incrementId = (string) $params->orderIncrementId;
        $order = $this->_orderFactory->create()->loadByAttribute('increment_id', $incrementId);

        // Check if the order can be shipped.
        if ( ! $order->canShip()) {
            throw new LocalizedException(
                __("Can't create shipment")
            );
        }

        // Create shipment.
        $shipment = $this->_orderConverter->toShipment($order);
        $shipment->addComment($params->comment, $params->email && $params->includeComment);

        return $shipment;
    }

    /**
     * Creates the relevant shipment lines.
     *
     * @param Magento\Sales\Model\Order\Shipment $shipment The shipment
     * @param array $skuQtys SKUs, and Quantities to update
     *
     * @throws LocalizedException
     */
    protected function _createShipmentLines($shipment, $skuQtys)
    {
        foreach ($skuQtys as $skuQty) {
            $qty = $skuQty->qty;
            $sku = $skuQty->sku;
            $orderItem = $this->_getOrderItem($shipment, $sku);
            $shipmentItem = $this->_orderConverter->itemToShipmentItem($orderItem)->setQty($qty);
            $shipment->addItem($shipmentItem);
        }
    }

    /**
     * Adds tracks to shipment.
     *
     * @param Shipment $shipment
     * @param array $params
     *
     * @eturn void
     */
    protected function _addTracks(Shipment $shipment, $params)
    {
        // Single Track
        if ($params->trackingNumber) {
            $this->_addTrack(
                $shipment,
                $params->courierName,
                $params->serviceName,
                $params->trackingNumber
            );
        }

        // Multi Track
        foreach ($params->tracks as $track) {
            $this->_addTrack(
                $shipment,
                $params->courierName,
                $params->serviceName,
                $track->trackingNumber
            );
        }
    }

    /**
     * Adds track to shipment.
     *
     * @param Shipment $shipment The shipment
     * @param array $params
     *
     * @return void
     */
    protected function _addTrack(
        Shipment $shipment,
        $courierName,
        $serviceName,
        $trackingNumber
    ) {
        $track = $this->_trackFactory->create();
        $track->setCarrierCode(Track::CUSTOM_CARRIER_CODE);
        $track->setTitle($courierName.' '.$serviceName);
        $track->setNumber($trackingNumber);
        $shipment->addTrack($track);
    }

    /**
     * Retrieves an order item by SKU.
     *
     * @param Shipment $shipment The shipment
     * @param string $sku The SKU
     *
     * @return mixed
     * @throws LocalizedException
     */
    protected function _getOrderItem($shipment, $sku)
    {
        $order = $shipment->getOrder();

        foreach ($order->getAllItems() as $orderItem) {
            if ($orderItem->getSku() === $sku) {
                return $orderItem;
            }
        }

        throw new LocalizedException(
            __('Order Item with SKU: "'.$sku.'" does not exist.')
        );
    }
}
