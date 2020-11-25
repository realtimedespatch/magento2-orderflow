<?php

namespace RealtimeDespatch\OrderFlow\System\Message\Import;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Notification\MessageInterface;
use Magento\Framework\UrlInterface;
use RealtimeDespatch\OrderFlow\Api\Data\ImportInterface;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Import\CollectionFactory as ImportCollectionFactory;

/**
 * Import Failure Message.
 *
 * Encapsulates an import failure message.
 */
class Failure implements MessageInterface
{
    const IDENTITY = 'ORDERFLOW_IMPORT_FAILURE';

    /**
     * @var ImportCollectionFactory
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
     * @var ImportInterface
     */
    protected $unreadImport;

    /**
     * @param ImportCollectionFactory $collectionFactory
     * @param AuthorizationInterface $authorization
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        ImportCollectionFactory $collectionFactory,
        AuthorizationInterface $authorization,
        UrlInterface $urlBuilder
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->authorization = $authorization;
        $this->urlBuilder = $urlBuilder;
        $this->unreadImport = $this->getLatestFailedImport();
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
        if (! $this->authorization->isAllowed('RealtimeDespatch_OrderFlow::orderflow_imports')) {
            return false;
        }

        return $this->unreadImport && $this->unreadImport->getId();
    }

    /**
     * Checks whether there is an unread import.
     *
     * @return DataObject
     */
    protected function getLatestFailedImport()
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
     * Retrieve message text
     *
     * @return string
     */
    public function getText()
    {
        $url = $this->urlBuilder->getUrl(
            'orderflow/import/view',
            ['import_id' => $this->unreadImport->getId()]
        );

        return __('A recent OrderFlow import contains failures. <a href="'.$url.'">View Details</a>');
    }

    /**
     * Retrieve message severity
     *
     * @return int
     */
    public function getSeverity()
    {
        return MessageInterface::SEVERITY_MAJOR;
    }
}
