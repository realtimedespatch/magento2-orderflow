<?php

namespace RealtimeDespatch\OrderFlow\Model\Service\Import;

use Exception;
use Magento\Framework\Event\ManagerInterface;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use \RealtimeDespatch\OrderFlow\Api\ImporterTypeInterface;

/**
 * Importer Service.
 *
 * Processes an Import Request.
 */
class Importer
{
    /* Events */
    const EVENT_SUCCESS = 'orderflow_export_success';
    const EVENT_FAILURE = 'orderflow_exception';

    /**
     * @var ImporterTypeInterface
     */
    public $type;

    /**
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * @param ImporterTypeInterface $type
     * @param ManagerInterface $eventManager
     */
    public function __construct(
        ImporterTypeInterface $type,
        ManagerInterface $eventManager
    ) {
        $this->type = $type;
        $this->eventManager = $eventManager;
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
            $import = $this->type->import($request);

            $this->eventManager->dispatch(
                self::EVENT_SUCCESS,
                ['import' => $import, 'type' => $this->type->getType()]
            );

            return true;
        } catch (Exception $ex) {
            $this->eventManager->dispatch(
                self::EVENT_FAILURE,
                ['exception' => $ex, 'type' => $this->type->getType(), 'process' => 'import']
            );

            return false;
        }
    }
}
