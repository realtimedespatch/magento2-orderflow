<?php

namespace RealtimeDespatch\OrderFlow\Ui\Component\Container;

use Exception;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Container;
use RealtimeDespatch\OrderFlow\Api\ExportRepositoryInterface;

class ExportLineListing extends Container
{
    const ORDER_EXPORT_LINE_LISTING_NS = 'order_export_line_listing';

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ExportRepositoryInterface
     */
    protected $exportRepository;

    /**
     * @param ContextInterface $context
     * @param RequestInterface $request
     * @param ExportRepositoryInterface $exportRepository
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        RequestInterface $request,
        ExportRepositoryInterface $exportRepository,
        array $components = [],
        array $data = []
    ) {
        $this->request = $request;
        $this->exportRepository = $exportRepository;

        $data = $this->configureRenderUrls($data);
        $data = $this->configureGridNamespace($data);

        parent::__construct($context, $components, $data);
    }

    /**
     * Configure Render URLs.
     *
     * This ensures the listing grid is updated correctly and filtered
     * against the current export that is being viewed.
     *
     * @param array $data
     * @return array
     */
    protected function configureRenderUrls(array $data)
    {
        $exportId = $this->request->getParam('export_id');

        $data['config']['render_url'] = $data['config']['render_url'].'export_id/'.$exportId;
        $data['config']['update_url'] = $data['config']['update_url'].'export_id/'.$exportId;

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
            $exportId = $this->request->getParam('export_id');
            $export = $this->exportRepository->get($exportId);

            if ($export->getEntity() === 'order') {
                $data['config']['ns'] = self::ORDER_EXPORT_LINE_LISTING_NS;
                $data['config']['dataScope'] = self::ORDER_EXPORT_LINE_LISTING_NS;
            }
        } catch (Exception $ex) {
            return $data;
        }

        return $data;
    }
}
