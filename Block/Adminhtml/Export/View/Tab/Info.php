<?php

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Export\View\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\WebsiteFactory;
use RealtimeDespatch\OrderFlow\Api\Data\ExportInterface;
use RealtimeDespatch\OrderFlow\Api\ExportRepositoryInterface;

/**
 * Export Info Tab.
 */
class Info extends Template implements TabInterface
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ExportInterface|null
     */
    protected $export;

    /**
     * @var WebsiteFactory
     */
    protected $websiteFactory;

    /**
     * @var ExportRepositoryInterface
     */
    protected $exportRepository;

    /**
     * @param Template\Context $context
     * @param RequestInterface $request
     * @param WebsiteFactory $websiteFactory
     * @param ExportRepositoryInterface $exportRepository
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        RequestInterface $request,
        WebsiteFactory $websiteFactory,
        ExportRepositoryInterface $exportRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->request = $request;
        $this->websiteFactory = $websiteFactory;
        $this->exportRepository = $exportRepository;
    }

    /**
     * Website Name Getter.
     *
     * @return string
     * @throws LocalizedException
     */
    public function getWebsiteName()
    {
        if (! $this->getExport()->getScopeId()) {
            return 'OrderFlow';
        }

        $website = $this->websiteFactory
            ->create()
            ->load($this->getExport()->getScopeId());

        return $website->getName();
    }

    /**
     * Export Getter.
     *
     * @return ExportInterface
     * @throws NoSuchEntityException
     */
    public function getExport()
    {
        $export = parent::getData('export');

        if ($export) {
            return $export;
        }

        $export = $this->exportRepository->get(
            $this->request->getParam('export_id')
        );

        parent::setData('export', $export);

        return $export;
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Is Ajax Loaded.
     */
    public function isAjaxLoaded()
    {
        return false;
    }
}
