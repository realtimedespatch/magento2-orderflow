<?php

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Import\View;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Registry;
use RealtimeDespatch\OrderFlow\Api\Data\ImportInterface;

/**
 * Import View Tabs
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
     * Retrieve available import
     *
     * @return ImportInterface
     * @throws LocalizedException
     */
    public function getImport()
    {
        if ($this->getData('import')) {
            return $this->getData('import');
        }
        if ($this->_coreRegistry->registry('current_import')) {
            return $this->_coreRegistry->registry('current_import');
        }
        if ($this->_coreRegistry->registry('import')) {
            return $this->_coreRegistry->registry('import');
        }
        throw new LocalizedException(__('We can\'t get the import instance right now.'));
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
        $this->setId('orderflow_import_view_tabs');
        $this->setDestElementId('orderflow_import_view');
        /** @noinspection PhpUndefinedMethodInspection */
        $this->setTitle(__('Import View'));
    }
}
