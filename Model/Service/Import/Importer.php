<?php

namespace RealtimeDespatch\OrderFlow\Model\Service\Import;

use Exception;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use \RealtimeDespatch\OrderFlow\Api\ImporterTypeInterface;

/**
 * Importer Service.
 *
 * Processes an Import Request.
 */
class Importer
{
    /**
     * @var ImporterTypeInterface
     */
    public $type;

    /**
     * @param ImporterTypeInterface $type
     */
    public function __construct(ImporterTypeInterface $type)
    {
        $this->type = $type;
    }

    /**
     * Processes an Import Request.
     *
     * @param RequestInterface $request
     *
     * @return boolean
     */
    public function import(RequestInterface $request)
    {
        if (! $this->type->isEnabled()) {
            return false;
        }

        try {
            $this->type->import($request);
        } catch (Exception $ex) {
            return false;
        }

        return true;
    }
}
