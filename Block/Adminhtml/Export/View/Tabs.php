<?php

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Export\View;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Registry;
use RealtimeDespatch\OrderFlow\Api\Data\ExportInterface;

/**
 * Export View Tabs
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Context $context
     * @param EncoderInterface $jsonEncoder
     * @param Session $authSession
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        EncoderInterface $jsonEncoder,
        Session $authSession,
        Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    /**
     * Retrieve available export
     *
     * @return ExportInterface
     * @throws LocalizedException
     */
    public function getExport()
    {
        if ($this->getData('export')) {
            return $this->getData('export');
        }
        if ($this->_coreRegistry->registry('current_export')) {
            return $this->_coreRegistry->registry('current_export');
        }
        if ($this->_coreRegistry->registry('export')) {
            return $this->_coreRegistry->registry('export');
        }
        throw new LocalizedException(__('We can\'t get the export instance right now.'));
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        /** @noinspection PhpUndefinedMethodInspection */
        $this->setId('orderflow_export_view_tabs');
        $this->setDestElementId('orderflow_export_view');
        /** @noinspection PhpUndefinedMethodInspection */
        $this->setTitle(__('Export View'));
    }
}
