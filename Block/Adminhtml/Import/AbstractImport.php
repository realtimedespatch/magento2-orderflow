<?php

namespace RealtimeDespatch\OrderFlow\Block\Adminhtml\Import;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use RealtimeDespatch\OrderFlow\Api\Data\ImportInterface;
use RealtimeDespatch\OrderFlow\Api\ImportRepositoryInterface;

class AbstractImport extends Widget
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ImportRepositoryInterface
     */
    protected $importRepository;

    /**
     * @var ImportInterface
     */
    protected $currentImport;

    /**
     * @param Context $context
     * @param RequestInterface $request
     * @param ImportRepositoryInterface $importRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        RequestInterface $request,
        ImportRepositoryInterface $importRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->request = $request;
        $this->importRepository = $importRepository;
    }

    /**
     * Import Getter.
     *
     * @return ImportInterface
     * @throws NoSuchEntityException
     */
    public function getImport()
    {
        if ($this->currentImport) {
            return $this->currentImport;
        }

        $this->currentImport = $this->importRepository->get(
            $this->request->getParam('import_id')
        );

        return $this->currentImport;
    }
}
