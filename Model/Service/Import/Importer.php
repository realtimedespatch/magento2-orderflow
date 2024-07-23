<?php

namespace RealtimeDespatch\OrderFlow\Model\Service\Import;

use \RealtimeDespatch\OrderFlow\Api\ImporterTypeInterface;

class Importer
{
    /**
     * @var \RealtimeDespatch\OrderFlow\Api\ImporterTypeInterface
     */
    public $_type;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @param \RealtimeDespatch\OrderFlow\Api\ImporterTypeInterface $type
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     */
    public function __construct(
        \RealtimeDespatch\OrderFlow\Api\ImporterTypeInterface $type,
        \Magento\Framework\Event\ManagerInterface $eventManager
    )
    {
        $this->_type = $type;
        $this->_eventManager = $eventManager;
    }

    /**
     * Imports a request.
     *
     * @param \RealtimeDespatch\OrderFlow\Model\Request $request
     *
     * @return void
     */
    public function import(\RealtimeDespatch\OrderFlow\Model\Request $request)
    {
        if ( ! $this->_type->isEnabled()) {
            return;
        }

        try {
            $import = $this->_type->import($request);

            $this->_eventManager->dispatch(
                'orderflow_import_success',
                ['import' => $import, 'type' => $this->_type->getType()]
            );
        }
        catch (\Exception $ex) {
            $this->_eventManager->dispatch(
                'orderflow_exception',
                ['exception' => $ex, 'type' => $this->_type->getType(), 'process' => 'import']
            );
        }
    }
}