<?php

namespace RealtimeDespatch\OrderFlow\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use RealtimeDespatch\OrderFlow\Api\Data\ExportInterface;
use RealtimeDespatch\OrderFlow\Api\ExportRepositoryInterface;

abstract class Export extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'RealtimeDespatch_OrderFlow::orderflow_exports';

    /**
     * {@inheritdoc}
     */
    protected $_publicActions = ['view', 'index'];

    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * @var ExportRepositoryInterface
     */
    protected $exportRepository;

    /**
     * @param Action\Context $context
     * @param PageFactory $pageFactory
     * @param ExportRepositoryInterface $exportRepository
     */
    public function __construct(
        Action\Context $context,
        PageFactory $pageFactory,
        ExportRepositoryInterface $exportRepository
    )
    {
        parent::__construct($context);

        $this->pageFactory = $pageFactory;
        $this->exportRepository = $exportRepository;
    }

    /**
     * Page Getter.
     *
     * @return Page
     */
    protected function getPage()
    {
        /* @var Page $resultPage */
        $resultPage = $this->pageFactory->create();
        $resultPage->addBreadcrumb(__('Exports'), __('Exports'));

        return $resultPage;
    }

    /**
     * Export Getter.
     *
     * @return ExportInterface
     * @throws NoSuchEntityException|CouldNotSaveException
     */
    protected function getExport()
    {
        $exportId = $this->getRequest()->getParam('export_id');
        $export = $this->exportRepository->get($exportId);
        $export->setViewedAt(date('Y-m-d H:i:s'));
        $this->exportRepository->save($export);

        return $export;
    }
}
