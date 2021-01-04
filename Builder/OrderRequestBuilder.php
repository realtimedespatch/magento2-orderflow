<?php

namespace RealtimeDespatch\OrderFlow\Builder;

use Exception;
use Magento\Sales\Model\Order;
use Magento\Store\Model\Website;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use RealtimeDespatch\OrderFlow\Api\ExportRequestBuilderInterface;
use RealtimeDespatch\OrderFlow\Helper\Export\Order as OrderHelper;
use RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface;
use Magento\Framework\Serialize\Serializer\Json;

class OrderRequestBuilder implements ExportRequestBuilderInterface
{
    /**
     * @var OrderHelper
     */
    protected $helper;

    /**
     * @var RequestBuilderInterface
     */
    protected $requestBuilder;

    /**
     * @var Json
     */
    protected $json;

    /**
     * @param OrderHelper $helper
     * @param RequestBuilderInterface $requestBuilder#
     * @param Json $json
     */
    public function __construct(
        OrderHelper $helper,
        RequestBuilderInterface $requestBuilder,
        Json $json
    ) {
        $this->helper = $helper;
        $this->requestBuilder = $requestBuilder;
        $this->json = $json;
    }

    /**
     * Builds new requests for orders that are available for creation and update.
     *
     * @param Website $website
     */
    public function build(Website $website)
    {
        $this->buildCreateOrderRequests($website);
    }

    /**
     * Builds create order requests.
     *
     * @param Website $website
     * @return void
     */
    protected function buildCreateOrderRequests(Website $website)
    {
        try {
            $orders = $this->helper->getCreateableOrders($website);

            if (count($orders) === 0) {
                return;
            }

            $this->requestBuilder->setScopeId($website->getId());

            foreach ($orders as $order) {
                $this->requestBuilder->addRequestLine(
                    $this->getRequestLine($order)
                );
            }

            $this->requestBuilder->saveRequest(
                RequestInterface::TYPE_EXPORT,
                RequestInterface::ENTITY_ORDER,
                RequestInterface::OP_CREATE
            );
        } catch (Exception $ex) {
            return;
        }
    }

    /**
     * Request Line Getter.
     *
     * @param Order $order
     * @return bool|string
     */
    protected function getRequestLine(Order $order)
    {
        return $this->json->serialize([
            'entity_id' => $order->getEntityId(),
            'increment_id' => $order->getIncrementId(),
        ]);
    }
}
