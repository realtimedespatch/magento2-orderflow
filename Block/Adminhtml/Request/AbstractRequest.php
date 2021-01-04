<?php

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Request;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\WebsiteFactory;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;
use RealtimeDespatch\OrderFlow\Api\RequestRepositoryInterface;

/**
 * Adminhtml request abstract block
 */
class AbstractRequest extends Widget
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var RequestRepositoryInterface
     */
    protected $requestRepository;

    /**
     * @var WebsiteFactory
     */
    protected $websiteFactory;

    /**
     * @var RequestInterface
     */
    protected $currentRequest;

    /**
     * @param Context $context
     * @param \Magento\Framework\App\RequestInterface $request
     * @param RequestRepositoryInterface $requestRepository
     * @param WebsiteFactory $websiteFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Framework\App\RequestInterface $request,
        RequestRepositoryInterface $requestRepository,
        WebsiteFactory $websiteFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->request = $request;
        $this->requestRepository = $requestRepository;
        $this->websiteFactory = $websiteFactory;
    }

    /**
     * Request Getter.
     *
     * @return RequestInterface
     * @throws NoSuchEntityException
     */
    public function getRtdRequest()
    {
        if ($this->currentRequest) {
            return $this->currentRequest;
        }

        $this->currentRequest = $this->requestRepository->get(
            $this->request->getParam('request_id')
        );

        return $this->currentRequest;
    }

    /**
     * Request Setter.
     *
     * @param RequestInterface $request
     * @return AbstractRequest
     */
    public function setRtdRequest(RequestInterface $request)
    {
        $this->currentRequest = $request;

        return $this;
    }
}
