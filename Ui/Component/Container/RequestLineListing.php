<?php

namespace RealtimeDespatch\OrderFlow\Ui\Component\Container;

use Exception;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Container;
use RealtimeDespatch\OrderFlow\Api\RequestRepositoryInterface;

class RequestLineListing extends Container
{
    const EXPORT_REQUEST_LINE_GRID_NS = 'request_export_line_listing';

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var RequestRepositoryInterface
     */
    protected $requestRepository;

    /**
     * @param ContextInterface $context
     * @param RequestInterface $request
     * @param RequestRepositoryInterface $requestRepository
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        RequestInterface $request,
        RequestRepositoryInterface $requestRepository,
        array $components = [],
        array $data = []
    ) {
        $this->request = $request;
        $this->requestRepository = $requestRepository;

        $data = $this->configureRenderUrls($data);
        $data = $this->configureGridNamespace($data);

        parent::__construct($context, $components, $data);
    }

    /**
     * Configure Render URLs.
     *
     * This ensures the listing grid is updated correctly and filtered
     * against the current request that is being viewed.
     *
     * @param array $data
     * @return array
     */
    protected function configureRenderUrls(array $data)
    {
        $requestId = $this->request->getParam('request_id');

        $data['config']['render_url'] = $data['config']['render_url'].'request_id/'.$requestId;
        $data['config']['update_url'] = $data['config']['update_url'].'request_id/'.$requestId;

        return $data;
    }

    /**
     * Configure Grid Namespace.
     *
     * @param array $data
     * @return array
     */
    protected function configureGridNamespace(array $data)
    {
        try {
            $requestId = $this->request->getParam('request_id');
            $request = $this->requestRepository->get($requestId);

            if ($request->isExport()) {
                $data['config']['ns'] = self::EXPORT_REQUEST_LINE_GRID_NS;
                $data['config']['dataScope'] = self::EXPORT_REQUEST_LINE_GRID_NS;
            }
        } catch (Exception $ex) {
            return $data;
        }

        return $data;
    }
}
