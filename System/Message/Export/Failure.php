<?php

namespace RealtimeDespatch\OrderFlow\System\Message\Export;

class Failure implements \Magento\Framework\Notification\MessageInterface
{
    const IDENTITY = 'ORDERFLOW_EXPORT_FAILURE';

    /**
     * @var \RealtimeDespatch\OrderFlow\Model\ExportFactory
     */
    protected  $_exportFactory;

    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \RealtimeDespatch\OrderFlow\Api\Data\ExportInterface
     */
    protected $_unreadExport;

    /**
     * Failure constructor.
     * @param \RealtimeDespatch\OrderFlow\Model\ExportFactory $exportFactory
     * @param \Magento\Framework\AuthorizationInterface $authorization
     * @param \Magento\Framework\UrlInterface $urlBuilder
     */
    public function __construct(
        \RealtimeDespatch\OrderFlow\Model\ExportFactory $exportFactory,
        \Magento\Framework\AuthorizationInterface $authorization,
        \Magento\Framework\UrlInterface $urlBuilder
    )
    {
        $this->_exportFactory = $exportFactory;
        $this->_authorization = $authorization;
        $this->_urlBuilder = $urlBuilder;
        $this->_unreadExport = $this->_getLatestFailedExport();
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
        if ( ! $this->_authorization->isAllowed('RealtimeDespatch_OrderFlow::orderflow_exports')) {
            return false;
        }

        return $this->_unreadExport && $this->_unreadExport->getId();
    }

    /**
     * Checks whether there is an unread export.
     *
     * @return array
     */
    protected function _getLatestFailedExport()
    {
        return $this->_exportFactory
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
            'orderflow/export/view',
            array('export_id' => $this->_unreadExport->getId())
        );

        return __('A recent OrderFlow export contains failures. <a href="%1">View Details</a', $url);
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