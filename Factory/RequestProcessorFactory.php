<?php

namespace RealtimeDespatch\OrderFlow\Factory;

use Magento\Framework\ObjectManagerInterface;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use RealtimeDespatch\OrderFlow\Api\RequestProcessorFactoryInterface;
use RealtimeDespatch\OrderFlow\Model\Service\Request\RequestProcessor;

/**
 * Request Processor Factory.
 *
 * Factory Class for Retrieving Request Processors.
 */
class RequestProcessorFactory implements RequestProcessorFactoryInterface
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Request Processor Getter.
     *
     * The object manager is required here as a factory.
     *
     * See: https://devdocs.magento.com/guides/v2.4/extension-dev-guide/object-manager.html
     *
     * @param RequestInterface $request
     * @return RequestProcessor
     */
    public function get(RequestInterface $request)
    {
        $className  = $request->getEntity();
        $className .= $request->getOperation();
        $className .= 'RequestProcessor';

        return $this->objectManager->create($className);
    }
}
