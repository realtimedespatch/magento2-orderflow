<?php

namespace RealtimeDespatch\OrderFlow\Model\Service\Export;

use Exception;
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
    /**
     * @var ExporterTypeInterface
     */
    public $type;

    /**
     * @param ExporterTypeInterface $type
     */
    public function __construct(ExporterTypeInterface $type)
    {
        $this->type = $type;
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
            $this->type->export($request);
        } catch (Exception $ex) {
            return false;
        }

        return true;
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
