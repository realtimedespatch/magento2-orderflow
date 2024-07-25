<?php

namespace RealtimeDespatch\OrderFlow\Model\Service\Export;

use \RealtimeDespatch\OrderFlow\Api\ExporterTypeInterface;

class Exporter
{
    /**
     * @var \RealtimeDespatch\OrderFlow\Api\ExporterTypeInterface
     */
    public $_type;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @param \RealtimeDespatch\OrderFlow\Api\ExporterTypeInterface $type
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     */
    public function __construct(
        \RealtimeDespatch\OrderFlow\Api\ExporterTypeInterface $type,
        \Magento\Framework\Event\ManagerInterface $eventManager
    )
    {
        $this->_type = $type;
        $this->_eventManager = $eventManager;
    }

    /**
     * Exports a request.
     *
     * @param \RealtimeDespatch\OrderFlow\Model\Request $request
     *
     * @return void
     */
    public function export(\RealtimeDespatch\OrderFlow\Model\Request $request)
    {
        if ( ! $this->_type->isEnabled($request->getScopeId()) && !in_array($this->_type::TYPE, ['Product', 'Order'])) {
            throw new \Exception($this->_type->getType().' exports are currently disabled. Please review your OrderFlow module configuration.');
        }

        try {
            $export = $this->_type->export($request);

            $this->_eventManager->dispatch(
                'orderflow_export_success',
                ['export' => $export, 'type' => $this->_type->getType()]
            );

            return $export;
        }
        catch (\Exception $ex) {
            $this->_eventManager->dispatch(
                'orderflow_exception',
                ['exception' => $ex, 'type' => $this->_type->getType(), 'process' => 'export']
            );
        }
    }
}
