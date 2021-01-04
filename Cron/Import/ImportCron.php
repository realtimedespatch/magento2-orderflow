<?php

namespace RealtimeDespatch\OrderFlow\Cron\Import;

use RealtimeDespatch\OrderFlow\Api\ImportHelperInterface;
use RealtimeDespatch\OrderFlow\Api\RequestProcessorFactoryInterface;

/**
 * Import Cron.
 *
 * Base Class for the Import Cron Jobs.
 */
class ImportCron
{
    /**
     * @var ImportHelperInterface
     */
    protected $helper;

    /**
     * @var RequestProcessorFactoryInterface
     */
    protected $reqProcessorFactory;

    /**
     * @param ImportHelperInterface $helper
     * @param RequestProcessorFactoryInterface $reqProcessorFactory
     */
    public function __construct(
        ImportHelperInterface $helper,
        RequestProcessorFactoryInterface $reqProcessorFactory
    ) {
        $this->helper = $helper;
        $this->reqProcessorFactory = $reqProcessorFactory;
    }

    /**
     * Execute Cron.
     *
     * @return $this|void
     */
    public function execute()
    {
        if (! $this->helper->isEnabled()) {
            return;
        }

        foreach ($this->helper->getImportableRequests() as $request) {
            $requestProcessor = $this->reqProcessorFactory->get($request);
            $requestProcessor->process($request);
        }
    }
}
