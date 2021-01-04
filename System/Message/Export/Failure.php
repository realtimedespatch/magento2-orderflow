<?php

namespace RealtimeDespatch\OrderFlow\System\Message\Export;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Notification\MessageInterface;
use Magento\Framework\UrlInterface;
use RealtimeDespatch\OrderFlow\Api\Data\ExportInterface;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Export\CollectionFactory;

class Failure implements MessageInterface
{
    const IDENTITY = 'ORDERFLOW_EXPORT_FAILURE';
    const ACL_RESOURCE = 'RealtimeDespatch_OrderFlow::orderflow_exports';

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

        return (boolean) $this->getUnreadFailedExport()->getId();
    }

    /**
     * Unread Export Failure Getter.
     *
     * @return ExportInterface
     */
    public function getUnreadFailedExport()
    {
        if ($this->unreadExport) {
            return $this->unreadExport;
        }

        $this->unreadExport = $this->collectionFactory->create()->getUnreadFailedExport();

        return $this->unreadExport;
    }

    /**
     * Retrieve message text.
     *
     * @return string
     */
    public function getText()
    {
        if (! $this->getUnreadFailedExport()->getId()) {
            return __('An unexpected error has occurred');
        }

        $url = $this->urlBuilder->getUrl(
            'orderflow/export/view',
            ['export_id' => $this->getUnreadFailedExport()->getId()]
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
        return MessageInterface::SEVERITY_MINOR;
    }
}
