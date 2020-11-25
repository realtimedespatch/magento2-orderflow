<?php

namespace RealtimeDespatch\OrderFlow\System\Message\Export;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Notification\MessageInterface;
use Magento\Framework\UrlInterface;
use RealtimeDespatch\OrderFlow\Api\Data\ExportInterface;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Export\CollectionFactory;

class Failure implements MessageInterface
{
    const IDENTITY = 'ORDERFLOW_EXPORT_FAILURE';

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var AuthorizationInterface
     */
    protected $authorization;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var ExportInterface
     */
    protected $unreadExport;

    /**
     * Failure constructor.
     * @param CollectionFactory $collectionFactory
     * @param AuthorizationInterface $authorization
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        AuthorizationInterface $authorization,
        UrlInterface $urlBuilder
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->authorization = $authorization;
        $this->urlBuilder = $urlBuilder;
        $this->unreadExport = $this->getLatestFailedExport();
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
        if (! $this->authorization->isAllowed('RealtimeDespatch_OrderFlow::orderflow_exports')) {
            return false;
        }

        return $this->unreadExport && $this->unreadExport->getId();
    }

    /**
     * Checks whether there is an unread export.
     *
     * @return DataObject
     */
    protected function getLatestFailedExport()
    {
        return $this
            ->collectionFactory
            ->create()
            ->addFieldToFilter('failures', ['gt' => 0])
            ->addFieldToFilter('viewed_at', ['null' => true])
            ->setOrder('created_at')
            ->setPageSize(1)
            ->setCurPage(1)
            ->getFirstItem();
    }

    /**
     * Retrieve message text.
     *
     * @return string
     */
    public function getText()
    {
        $url = $this->urlBuilder->getUrl(
            'orderflow/export/view',
            ['export_id' => $this->unreadExport->getId()]
        );

        return __('A recent OrderFlow export contains failures. <a href="'.$url.'">View Details</a>');
    }

    /**
     * Retrieve message severity.
     *
     * @return int
     */
    public function getSeverity()
    {
        return MessageInterface::SEVERITY_MAJOR;
    }
}
