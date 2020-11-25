<?php

namespace RealtimeDespatch\OrderFlow\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use RealtimeDespatch\OrderFlow\Api\RequestRepositoryInterface;

abstract class Request extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'RealtimeDespatch_OrderFlow::orderflow_requests';

    /**
     * {@inheritdoc}
     */
    protected $_publicActions = ['view', 'index'];

    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * @var RequestRepositoryInterface
     */
    protected $requestRepository;

    /**
     * @param Action\Context $context
     * @param PageFactory $pageFactory
     * @param RequestRepositoryInterface $requestRepository
     */
    public function __construct(
        Action\Context $context,
        PageFactory $pageFactory,
        RequestRepositoryInterface $requestRepository
    )
    {
        parent::__construct($context);

        $this->pageFactory = $pageFactory;
        $this->requestRepository = $requestRepository;
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
        $resultPage->addBreadcrumb(__('Requests'), __('Requests'));

        return $resultPage;
    }

    /**
     * Request Getter.
     *
     * @return RequestInterface
     * @throws NoSuchEntityException
     */
    protected function getOrderFlowRequest()
    {
        return $this->requestRepository->get(
            $this->getRequest()->getParam('request_id')
        );
    }
}
