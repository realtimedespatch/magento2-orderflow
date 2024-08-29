<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Product\Edit\Info;

use Magento\Catalog\Model\Product;
use Magento\Framework\App\RequestInterface;
use RealtimeDespatch\OrderFlow\Block\Adminhtml\Product\Edit\Info\OrderFlow;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Message\ManagerInterface;
use RealtimeDespatch\OrderFlow\Helper\Admin\Info as AdminInfoHelper;
use RealtimeDespatch\OrderFlow\Helper\Api as ApiHelper;

/**
 * Class OrderFlowTest
 * @package RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Product\Edit\Info
 */
class OrderFlowTest extends \PHPUnit\Framework\TestCase
{
    protected OrderFlow $block;
    protected Context $mockContext;
    protected Registry $mockRegistry;
    protected HttpRequest $mockHttpRequest;
    protected ManagerInterface $mockMessageManager;
    protected AdminInfoHelper $mockAdminInfoHelper;
    protected ApiHelper $mockApiHelper;
    protected Product $mockProduct;
    protected RequestInterface $mockRequest;

    protected function setUp(): void
    {
        $this->mockContext = $this->createMock(Context::class);
        $this->mockRegistry = $this->createMock(Registry::class);
        $this->mockHttpRequest = $this->createMock(HttpRequest::class);
        $this->mockMessageManager = $this->createMock(ManagerInterface::class);
        $this->mockAdminInfoHelper = $this->createMock(AdminInfoHelper::class);
        $this->mockApiHelper = $this->createMock(ApiHelper::class);
        $this->mockProduct = $this->createMock(Product::class);
        $this->mockRequest = $this->createMock(RequestInterface::class);

        $this->mockContext
            ->method('getRequest')
            ->willReturn($this->mockRequest);

        $this->block = new OrderFlow(
            $this->mockContext,
            $this->mockRegistry,
            $this->mockHttpRequest,
            $this->mockMessageManager,
            $this->mockAdminInfoHelper,
            $this->mockApiHelper
        );
    }

    public function testCanDisplayAdminInfo(): void
    {
        $this->mockAdminInfoHelper
            ->method('isEnabled')
            ->willReturn(true);

        $this->assertTrue($this->block->canDisplayAdminInfo());
    }

    /**
     * @dataProvider getUrlDataProvider
     * @param string $method
     * @param string $url
     * @return void
     */
    public function testGetProductUrl(string $method, string $url): void
    {
        $storeId = 1;

        $this->mockRegistry
            ->method('registry')
            ->with('product')
            ->willReturn($this->mockProduct);

        $this->mockProduct
            ->method('getSku')
            ->willReturn('TEST-SKU');

        $this->mockRequest
            ->method('getParam')
            ->with('store', 0)
            ->willReturn($storeId);

        $this->mockApiHelper
            ->expects($this->once())
            ->method('getEndpoint')
            ->with($storeId)
            ->willReturn('http://localhost/orderflow/');

        $this->mockApiHelper
            ->expects($this->once())
            ->method('getChannel')
            ->with($storeId)
            ->willReturn('test_channel');

        $expectedUrl = "http://localhost/orderflow/$url/referenceDetail.htm?externalReferenceTEST-SKU&channel=test_channel";

        $result = $this->block->{$method}();
        $this->assertEquals($expectedUrl, $result);
    }

    /**
     * @dataProvider getUrlDataProvider
     * @param string $method
     * @param string $url
     * @return void
     */
    public function testGetProductUrlNoSku(string $method, string $url): void
    {
        $this->mockRegistry
            ->method('registry')
            ->with('product')
            ->willReturn($this->mockProduct);

        $result = $this->block->{$method}();
        $this->assertEquals('', $result);
    }

    public function getUrlDataProvider(): array
    {
        return [
            [
                'getProductUrl',
                'inventory/product'
            ],
            [
                'getInventoryUrl',
                'inventory/inventory'
            ]
        ];
    }
}