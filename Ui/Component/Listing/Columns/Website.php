<?php

namespace RealtimeDespatch\OrderFlow\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class Website extends Column
{
    /**
     * @var Magento\Store\Model\WebsiteFactory
     */
    protected $_websiteFactory;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Magento\Store\Model\WebsiteFactory $websiteFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        array $components = [],
        array $data = []
    ) {
        $this->_websiteFactory = $websiteFactory;
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
                if (isset($item['scope_id'])) {
                    $website = $this->_websiteFactory->create()->load((integer) $item['scope_id']);
                    $item[$this->getData('name')] = $website->getName();
                } else {
                    $item[$this->getData('name')] = 'OrderFlow';
                }
            }
        }

        return $dataSource;
    }
}