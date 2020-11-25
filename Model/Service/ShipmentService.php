<?php

/** @noinspection PhpUndefinedClassInspection */

namespace RealtimeDespatch\OrderFlow\Model\Service;

use Exception;
use Magento\Framework\DB\Transaction;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Convert\Order;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\Order\Shipment\Track;
use Magento\Sales\Model\OrderFactory;
use Magento\Shipping\Model\Order\TrackFactory;
use Magento\Shipping\Model\ShipmentNotifier;
use RealtimeDespatch\OrderFlow\Api\ShipmentManagementInterface;
use RealtimeDespatch\OrderFlow\Helper\Import\Shipment as ShipmentImportHelper;

/**
 * Class ShipmentService
 *
 * Service to process shipments to customers.
 *
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ShipmentService implements ShipmentManagementInterface
{
    /**
     * @var ShipmentImportHelper
     */
    protected $_helper;

    /**
     * @param OrderFactory $orderFactory
     */
    protected $orderFactory;

    /**
     * @param TrackFactory $trackFactory
     */
    protected $trackFactory;

    /**
     * @param Order $orderConverter
     */
    protected $orderConverter;

    /**
     * @param ShipmentNotifier $shipmentNotifier
     */
    protected $shipmentNotifier;

    /**
     * @var Transaction
     */
    protected $transaction;

    /**
     * @param ShipmentImportHelper $helper
     * @param OrderFactory $orderFactory
     * @param TrackFactory $trackFactory
     * @param Order $orderConverter
     * @param ShipmentNotifier $shipmentNotifier
     * @param Transaction $transaction
     */
    public function __construct(
        ShipmentImportHelper $helper,
        OrderFactory $orderFactory,
        TrackFactory $trackFactory,
        Order $orderConverter,
        ShipmentNotifier $shipmentNotifier,
        Transaction $transaction
    ) {
        $this->_helper = $helper;
        $this->orderFactory = $orderFactory;
        $this->trackFactory = $trackFactory;
        $this->orderConverter = $orderConverter;
        $this->shipmentNotifier = $shipmentNotifier;
        $this->transaction = $transaction;
    }

    /**
     * Creates a new shipment
     *
     * @param mixed $params Shipment Params
     *
     * @return Shipment
     * @throws LocalizedException
     */
    public function createShipment($params)
    {
        try {
            // Create shipment.
            $shipment = $this->_createShipment($params);

            // Add line(s).
            $this->_createShipmentLines($shipment, $params->skuQtys);

            // Add track.
            if ($params->trackingNumber) {
                $this->_addTrack($shipment, $params);
            }

            // Register shipment.
            $shipment->register();
            /** @noinspection PhpUndefinedMethodInspection */
            $shipment->getOrder()->setIsInProcess(true);

            // Save shipment.
            $this->transaction->addObject($shipment);
            $this->transaction->addObject($shipment->getOrder());
            $this->transaction->save();

            // Notify customer.
            if ($params->email) {
                $this->shipmentNotifier->notify($shipment);
            }

            return $shipment;
        } catch (Exception $e) {
            throw new LocalizedException(
                __($e->getMessage())
            );
        }
    }

    /**
     * Create Shipment.
     *
     * @param mixed $params
     *
     * @return Shipment
     * @throws LocalizedException
     */
    protected function _createShipment($params)
    {
        $incrementId = (string) $params->orderIncrementId;
        $order = $this->orderFactory->create()->loadByAttribute('increment_id', $incrementId);

        // Check if the order can be shipped.
        if (! $order->canShip()) {
            throw new LocalizedException(
                __("Can't create shipment")
            );
        }

        // Create shipment.
        $shipment = $this->orderConverter->toShipment($order);
        $shipment->addComment($params->comment, $params->email && $params->includeComment);

        return $shipment;
    }

    /**
     * Create Shipment Lines.
     *
     * @param Shipment $shipment
     * @param mixed $skuQtys
     *
     * @throws LocalizedException
     */
    protected function _createShipmentLines(Shipment $shipment, $skuQtys)
    {
        foreach ($skuQtys as $skuQty) {
            $qty = $skuQty->qty;
            $sku = $skuQty->sku;
            $orderItem = $this->_getOrderItem($shipment, $sku);
            $shipmentItem = $this->orderConverter->itemToShipmentItem($orderItem)->setQty($qty);
            $shipment->addItem($shipmentItem);
        }
    }

    /**
     * Add Tracking.
     *
     * @param Shipment $shipment The shipment
     * @param mixed $params
     *
     * @return void
     */
    protected function _addTrack(Shipment $shipment, $params)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $track = $this->trackFactory->create();
        $track->setCarrierCode(Track::CUSTOM_CARRIER_CODE);
        $track->setTitle($params->courierName.' '.$params->serviceName);
        $track->setNumber($params->trackingNumber);
        $shipment->addTrack($track);
    }

    /**
     * Retrieves an order item by SKU.
     *
     * @param Shipment $shipment
     * @param string $sku
     *
     * @return mixed
     * @throws LocalizedException
     */
    protected function _getOrderItem(Shipment $shipment, string $sku)
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
