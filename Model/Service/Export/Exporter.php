<?php

namespace RealtimeDespatch\OrderFlow\Model\Service\Export;

use Exception;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use RealtimeDespatch\OrderFlow\Api\ExporterTypeInterface;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;

/**
 * Exporter Service.
 *
 * Processes an Export Request.
 */
class Exporter
{
    /* Event Names */
    const EVENT_NAME_SUCCESS = 'orderflow_export_success';
    const EVENT_NAME_FAILURE = 'orderflow_exception';

    /**
     * @var ExporterTypeInterface
     */
    public $type;

    /**
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * @param ExporterTypeInterface $type
     * @param ManagerInterface $eventManager
     */
    public function __construct(
        ExporterTypeInterface $type,
        ManagerInterface $eventManager
    ) {
        $this->type = $type;
        $this->eventManager = $eventManager;
    }

    /**
     * Processes an Export Request.
     *
     * @param RequestInterface $request
     *
     * @return boolean
     * @throws LocalizedException
     */
    public function export(RequestInterface $request)
    {
        if (! $this->type->isEnabled($request->getScopeId())) {
            $this->throwDisabledException();
        }

        try {
            $export = $this->type->export($request);

            $this->eventManager->dispatch(
                self::EVENT_NAME_SUCCESS,
                ['export' => $export, 'type' => $this->type->getType()]
            );

            return true;
        } catch (Exception $ex) {
            $this->eventManager->dispatch(
                self::EVENT_NAME_FAILURE,
                ['exception' => $ex, 'type' => $this->type->getType(), 'process' => 'export']
            );

            return false;
        }
    }

    /**
     * Throws a Disabled Export Exception.
     *
     * @throws LocalizedException
     */
    protected function throwDisabledException()
    {
        $msg  = $this->type->getType();
        $msg .= ' exports are currently disabled. Please review your OrderFlow module configuration.';

        throw new LocalizedException(__($msg));
    }
}
