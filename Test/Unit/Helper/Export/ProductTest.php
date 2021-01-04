<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Helper\Export;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Website;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Helper\Export\Product;
use RealtimeDespatch\OrderFlow\Model\Source\Export\Status;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Request\CollectionFactory as RequestCollectionFactory;

class ProductTest extends TestCase
{
    /**
     * @var MockObject
     */
    protected $scopeConfigMock;

    /**
     * @var MockObject
     */
    protected $productCollectionFactory;

    /**
     * @var RequestCollectionFactory
     */
    protected $reqCollectionFactory;

    /**
     * @var Product
     */
    protected $helper;

    public function setUp()
    {
        $this->scopeConfigMock = $this->getMockBuilder(ScopeConfigInterface::class)
            ->getMock();

        $this->productCollectionFactory = $this->getMockBuilder(CollectionFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->reqCollectionFactory = $this->getMockBuilder(RequestCollectionFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $context->expects($this->once())
            ->method('getScopeConfig')
            ->willReturn($this->scopeConfigMock);

        $this->helper = new Product(
            $context,
            $this->productCollectionFactory,
            $this->reqCollectionFactory
        );
    }

    /**
     * @dataProvider dataProviderIsEnabled
     * @param $configPath
     * @param $configValue
     * @param $returnValue
     * @param $scopeId
     */
    public function testIsEnabled($configPath, $configValue, $returnValue, $scopeId)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('isSetFlag')
            ->with($configPath, ScopeInterface::SCOPE_STORE, $scopeId)
            ->willReturn($configValue);

        $this->assertEquals($returnValue, $this->helper->isEnabled($scopeId));
    }

    /**
     * @return array
     */
    public function dataProviderIsEnabled()
    {
        return [
            ['orderflow_product_export/settings/is_enabled', true, true, 1],
            ['orderflow_product_export/settings/is_enabled', false, false, 1],
        ];
    }

    /**
     * @dataProvider dataProviderGetBatchSize
     * @param $configPath
     * @param $configValue
     * @param $scopeId
     * @param $returnValue
     */
    public function testGetBatchSize($configPath, $configValue, $scopeId, $returnValue)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->with($configPath, ScopeInterface::SCOPE_WEBSITE)
            ->willReturn($configValue);

        $this->assertEquals($returnValue, $this->helper->getBatchSize($scopeId));
    }

    /**
     * @return array
     */
    public function dataProviderGetBatchSize()
    {
        return [
            ['orderflow_product_export/settings/batch_size', null, 1, 0],
            ['orderflow_product_export/settings/batch_size', false, 1, 0],
            ['orderflow_product_export/settings/batch_size', 10, 1, 10],
        ];
    }

    /**
     * @dataProvider dataProviderGetStoreId
     * @param $configPath
     * @param $configValue
     * @param $scopeId
     * @param $returnValue
     */
    public function testGetStoreId($configPath, $configValue, $scopeId, $returnValue)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->with($configPath, ScopeInterface::SCOPE_WEBSITE)
            ->willReturn($configValue);

        $this->assertEquals($returnValue, $this->helper->getStoreId($scopeId));
    }

    /**
     * @return array
     */
    public function dataProviderGetStoreId()
    {
        return [
            ['orderflow_product_export/settings/store_id', null, 1, 0],
            ['orderflow_product_export/settings/store_id', false, 1, 0],
            ['orderflow_product_export/settings/store_id', 1, 1, 1],
        ];
    }

    /**
     * @depends testGetBatchSize
     * @depends testGetStoreId
     */
    public function testGetCreateableProducts()
    {
        $websiteId = 1;
        $storeId = 2;
        $batchSize = 10;
        $pageNum = 1;

        $this->scopeConfigMock->expects($this->exactly(2))
            ->method('getValue')
            ->withConsecutive(
                ['orderflow_product_export/settings/store_id'],
                ['orderflow_product_export/settings/batch_size']
            )
            ->will($this->onConsecutiveCalls($storeId, $batchSize));

        $website = $this->getMockBuilder(Website::class)
            ->disableOriginalConstructor()
            ->getMock();

        $website->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($websiteId);

        $productCollection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $productCollection->expects($this->once())
            ->method('addAttributeToSelect')
            ->with('*')
            ->willReturn($productCollection);

        $productCollection->expects($this->exactly(3))
            ->method('addAttributeToFilter')
            ->withConsecutive(
                ['type_id', ['eq' => 'simple']],
                ['orderflow_export_date', ['null' => true]],
                [
                    [
                        ['attribute' => 'orderflow_export_status', 'null' => true],
                        ['attribute' => 'orderflow_export_status', ['neq' => [Status::STATUS_QUEUED]]],
                    ],
                    '',
                    'left'
                ]
            )
            ->willReturn($productCollection);

        $productCollection->expects($this->once())
            ->method('setStore')
            ->with($storeId)
            ->willReturn($productCollection);

        $productCollection->expects($this->once())
            ->method('setPage')
            ->with($pageNum, $batchSize)
            ->willReturn($productCollection);

        $this->productCollectionFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($productCollection);

        $this->assertSame($productCollection, $this->helper->getCreateableProducts($website));
    }

    /**
     * @depends testGetBatchSize
     * @depends testGetStoreId
     */
    public function testGetUpdateableProducts()
    {
        $websiteId = 1;
        $storeId = 2;
        $batchSize = 10;
        $pageNum = 1;

        $this->scopeConfigMock->expects($this->exactly(2))
            ->method('getValue')
            ->withConsecutive(
                ['orderflow_product_export/settings/store_id'],
                ['orderflow_product_export/settings/batch_size']
            )
            ->will($this->onConsecutiveCalls($storeId, $batchSize));

        $website = $this->getMockBuilder(Website::class)
            ->disableOriginalConstructor()
            ->getMock();

        $website->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($websiteId);

        $productCollection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $productCollection->expects($this->once())
            ->method('addAttributeToSelect')
            ->with('*')
            ->willReturn($productCollection);

        $productCollection->expects($this->exactly(3))
            ->method('addAttributeToFilter')
            ->withConsecutive(
                ['type_id', ['eq' => 'simple']],
                ['orderflow_export_date', ['notnull' => true]],
                ['orderflow_export_status', ['eq' => Status::STATUS_PENDING]]
            )
            ->willReturn($productCollection);

        $productCollection->expects($this->once())
            ->method('setStore')
            ->with($storeId)
            ->willReturn($productCollection);

        $productCollection->expects($this->once())
            ->method('setPage')
            ->with($pageNum, $batchSize)
            ->willReturn($productCollection);

        $this->productCollectionFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($productCollection);

        $this->assertSame($productCollection, $this->helper->getUpdateableProducts($website));
    }
}
