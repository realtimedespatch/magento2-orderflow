<?php

namespace RealtimeDespatch\OrderFlow\Ui\Component\Container;

use Exception;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Container;
use RealtimeDespatch\OrderFlow\Api\ImportRepositoryInterface;

class ImportLineListing extends Container
{
    const SHIPMENT_IMPORT_LINE_LISTING_NS = 'shipment_import_line_listing';

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ImportRepositoryInterface
     */
    protected $importRepository;

    /**
     * @param ContextInterface $context
     * @param RequestInterface $request
     * @param ImportRepositoryInterface $importRepository
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        RequestInterface $request,
        ImportRepositoryInterface $importRepository,
        array $components = [],
        array $data = []
    ) {
        $this->request = $request;
        $this->importRepository = $importRepository;

        $data = $this->configureRenderUrls($data);
        $data = $this->configureGridNamespace($data);

        parent::__construct($context, $components, $data);
    }

    /**
     * Configure Render URLs.
     *
     * This ensures the listing grid is updated correctly and filtered
     * against the current import that is being viewed.
     *
     * @param array $data
     * @return array
     */
    protected function configureRenderUrls(array $data)
    {
        $importId = $this->request->getParam('import_id');

        $data['config']['render_url'] = $data['config']['render_url'].'import_id/'.$importId;
        $data['config']['update_url'] = $data['config']['update_url'].'import_id/'.$importId;

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
            $importId = $this->request->getParam('import_id');
            $import = $this->importRepository->get($importId);

            if ($import->getEntity() === 'shipment') {
                $data['config']['ns'] = self::SHIPMENT_IMPORT_LINE_LISTING_NS;
                $data['config']['dataScope'] = self::SHIPMENT_IMPORT_LINE_LISTING_NS;
            }
        } catch (Exception $ex) {
            return $data;
        }

        return $data;
    }
}
