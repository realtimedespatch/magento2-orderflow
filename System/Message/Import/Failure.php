<?php

namespace RealtimeDespatch\OrderFlow\System\Message\Import;

/**
 * Class Failure
 * @package RealtimeDespatch\OrderFlow\System\Message\Import
 */
class Failure implements \Magento\Framework\Notification\MessageInterface
{
    const IDENTITY = 'ORDERFLOW_IMPORT_FAILURE';

    /**
     * @var \RealtimeDespatch\OrderFlow\Model\ImportFactory
     */
    protected  $_importFactory;

    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \RealtimeDespatch\OrderFlow\Api\Data\ImportInterface
     */
    protected $_unreadImport;

    /**
     * Failure constructor.
     * @param \RealtimeDespatch\OrderFlow\Model\ImportFactory $importFactory
     * @param \Magento\Framework\AuthorizationInterface $authorization
     * @param \Magento\Framework\UrlInterface $urlBuilder
     */
    public function __construct(
        \RealtimeDespatch\OrderFlow\Model\ImportFactory $importFactory,
        \Magento\Framework\AuthorizationInterface $authorization,
        \Magento\Framework\UrlInterface $urlBuilder
    )
    {
        $this->_importFactory = $importFactory;
        $this->_authorization = $authorization;
        $this->_urlBuilder = $urlBuilder;
        $this->_unreadImport = $this->_getLatestFailedImport();
    }

    /**
     * Retrieve unique message identity
     *
     * @return string
     */
    public function getIdentity()
    {
        return self::IDENTITY;
    }

    /**
     * Check whether the message is displayable.
     *
     * @return bool
     */
    public function isDisplayed()
    {
        if ( ! $this->_authorization->isAllowed('RealtimeDespatch_OrderFlow::orderflow_imports')) {
            return false;
        }

        return $this->_unreadImport && $this->_unreadImport->getId();
    }

    /**
     * Checks whether there is an unread import.
     *
     * @return array
     */
    protected function _getLatestFailedImport()
    {
        return $this->_importFactory
            ->create()
            ->getCollection()
            ->addFieldToFilter('failures', ['gt' => 0])
            ->addFieldToFilter('viewed_at', ['null' => true])
            ->setOrder('created_at')
            ->setPageSize(1)
            ->setCurPage(1)
            ->getFirstItem();
    }

    /**
     * Retrieve message text
     *
     * @return string
     */
    public function getText()
    {
        $url = $this->_urlBuilder->getUrl(
            'orderflow/import/view',
            array('import_id' => $this->_unreadImport->getId())
        );

        return __('A recent OrderFlow import contains failures. <a href="%1">View Details</a>', $url);
    }

    /**
     * Retrieve message severity
     *
     * @return int
     */
    public function getSeverity()
    {
        return \Magento\Framework\Notification\MessageInterface::SEVERITY_MAJOR;
    }
}