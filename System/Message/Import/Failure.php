<?php

namespace RealtimeDespatch\OrderFlow\System\Message\Import;

use Magento\Framework\AuthorizationInterface;
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
    const ACL_RESOURCE = 'RealtimeDespatch_OrderFlow::orderflow_imports';

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
        if (! $this->authorization->isAllowed(self::ACL_RESOURCE)) {
            return false;
        }

        return (boolean) $this->getUnreadFailedImport()->getId();
    }

    /**
     * Checks whether there is an unread import.
     *
     * @return ImportInterface
     */
    public function getUnreadFailedImport()
    {
        if ($this->unreadImport) {
            return $this->unreadImport;
        }

        $this->unreadImport = $this->collectionFactory->create()->getUnreadFailedImport();

        return $this->unreadImport;
    }

    /**
     * Retrieve message text
     *
     * @return string
     */
    public function getText()
    {
        if (! $this->getUnreadFailedImport()->getId()) {
            return __('An unexpected error has occurred');
        }

        $url = $this->urlBuilder->getUrl(
            'orderflow/import/view',
            ['import_id' => $this->getUnreadFailedImport()->getId()]
        );

        return __('A recent OrderFlow import contains failures. <a href="'.$url.'">View Details</a>');
    }

    /**
     * Retrieve message severity.
     *
     * @return int
     */
    public function getSeverity()
    {
        return MessageInterface::SEVERITY_MINOR;
    }
}
