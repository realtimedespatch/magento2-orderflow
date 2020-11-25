<?php

namespace RealtimeDespatch\OrderFlow\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use RealtimeDespatch\OrderFlow\Api\Data\ImportInterface;
use RealtimeDespatch\OrderFlow\Api\ImportRepositoryInterface;

abstract class Import extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'RealtimeDespatch_OrderFlow::orderflow_imports';

    /**
     * {@inheritdoc}
     */
    protected $_publicActions = ['view', 'index'];

    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * @var ImportRepositoryInterface
     */
    protected $importRepository;

    /**
     * @param Action\Context $context
     * @param PageFactory $pageFactory
     * @param ImportRepositoryInterface $importRepository
     */
    public function __construct(
        Action\Context $context,
        PageFactory $pageFactory,
        ImportRepositoryInterface $importRepository
    )
    {
        parent::__construct($context);

        $this->pageFactory = $pageFactory;
        $this->importRepository = $importRepository;
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
        $resultPage->addBreadcrumb(__('Imports'), __('Imports'));

        return $resultPage;
    }

    /**
     * Import Getter.
     *
     * @return ImportInterface
     * @throws NoSuchEntityException|CouldNotSaveException
     */
    protected function getImport()
    {
        $importId = $this->getRequest()->getParam('import_id');
        $import = $this->importRepository->get($importId);
        $import->setViewedAt(date('Y-m-d H:i:s'));
        $this->importRepository->save($import);

        return $import;
    }
}
