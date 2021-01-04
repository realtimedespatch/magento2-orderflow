<?php

namespace RealtimeDespatch\OrderFlow\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\WebsiteFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Website extends Column
{
    /**
     * @var WebsiteFactory
     */
    protected $websiteFactory;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param WebsiteFactory $websiteFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        WebsiteFactory $websiteFactory,
        array $components = [],
        array $data = []
    ) {
        $this->websiteFactory = $websiteFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[$this->getData('name')])) {
                    $website = $this->websiteFactory->create()->load((integer) $item[$this->getData('name')]);
                    $item[$this->getData('name')] = $website->getName();
                } else {
                    $item[$this->getData('name')] = 'OrderFlow';
                }
            }
        }

        return $dataSource;
    }
}
