<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Controller\Adminhtml\Product;

use Magento\Backend\Model\View\Result\Redirect;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Catalog\Model\ProductRepository;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use RealtimeDespatch\OrderFlow\Controller\Adminhtml\Product\Export as ExportController;
use Magento\Backend\App\Action\Context;
use RealtimeDespatch\OrderFlow\Helper\Export\Product as ProductExportHelper;
use RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface;
use RealtimeDespatch\OrderFlow\Model\Request as RequestModel;
use RealtimeDespatch\OrderFlow\Model\Export;
use Magento\Framework\App\ObjectManager;
use RealtimeDespatch\OrderFlow\Model\Builder\RequestBuilder;
use RealtimeDespatch\OrderFlow\Model\Service\Request\RequestProcessor;
use Magento\Framework\App\ActionFlag;

/**
 * Class ExportTest
 * @package RealtimeDespatch\OrderFlow\Test\Unit\Controller\Adminhtml\Product
 */
class ExportTest extends \PHPUnit\Framework\TestCase
{
    protected ExportController $controller;
    protected Context $mockContext;
    protected ProductExportHelper $mockProductExportHelper;
    protected RequestBuilderInterface $mockRequestBuilder;
    protected ProductRepository $mockProductRepository;
    protected StoreManagerInterface $mockStoreManager;
    protected RedirectFactory $mockResultRedirectFactory;
    protected Redirect $mockResultRedirect;
    protected RequestInterface $mockRequest;
    protected Product $mockProduct;
    protected Store $mockStore;
    protected ObjectManager $mockObjectManager;
    protected RequestProcessor $mockRequestProcessor;
    protected RequestModel $mockRequestModel;
    protected Export $mockExport;
    protected ManagerInterface $mockMessageManager;
    protected ActionFlag $mockActionFlag;

    protected int $productId = 1;
    protected string $productSku = 'SKU123';
    protected int $storeId = 2;
    protected int $websiteId = 3;
    protected string $orderIncrementId = '000000001';

    protected function setUp(): void
    {
        $this->mockContext = $this->createMock(Context::class);
        $this->mockProductExportHelper = $this->createMock(ProductExportHelper::class);
        $this->mockRequestBuilder = $this->createMock(RequestBuilder::class);
        $this->mockProductRepository = $this->createMock(ProductRepository::class);
        $this->mockStoreManager = $this->createMock(StoreManagerInterface::class);
        $this->mockResultRedirectFactory = $this->createMock(RedirectFactory::class);
        $this->mockResultRedirect = $this->createMock(Redirect::class);
        $this->mockRequest = $this->createMock(RequestInterface::class);
        $this->mockProduct = $this->createMock(Product::class);
        $this->mockStore = $this->createMock(Store::class);
        $this->mockObjectManager = $this->createMock(ObjectManager::class);
        $this->mockRequestProcessor = $this->createMock(RequestProcessor::class);
        $this->mockRequestModel = $this->createMock(RequestModel::class);
        $this->mockExport = $this->createMock(Export::class);
        $this->mockMessageManager = $this->createMock(ManagerInterface::class);
        $this->mockActionFlag = $this->createMock(ActionFlag::class);

        $this->mockContext->method('getResultRedirectFactory')
            ->willReturn($this->mockResultRedirectFactory);

        $this->mockResultRedirectFactory
            ->method('create')
            ->willReturn($this->mockResultRedirect);

        $this->mockContext
            ->method('getRequest')
            ->willReturn($this->mockRequest);

        $this->mockContext
            ->method('getObjectManager')
            ->willReturn($this->mockObjectManager);

        $this->mockContext
            ->method('getMessageManager')
            ->willReturn($this->mockMessageManager);

        $this->mockContext
            ->method('getActionFlag')
            ->willReturn($this->mockActionFlag);

        $this->mockRequest
            ->method('getParam')
            ->willReturnCallback(function(string $param) {
                if ($param == 'id') {
                    return $this->productId;
                } else if ($param == 'store') {
                    return $this->storeId;
                } else {
                    return null;
                }
            });

        $this->mockProductRepository
            ->method('getById')
            ->with($this->productId)
            ->willReturn($this->mockProduct);

        $this->mockProduct
            ->method('getStoreId')
            ->willReturn($this->storeId);

        $this->mockProduct
            ->method('getEntityId')
            ->willReturn($this->productId);

        $this->mockStoreManager
            ->method('getStore')
            ->with($this->storeId)
            ->willReturn($this->mockStore);

        $this->mockStore
            ->method('getWebsiteId')
            ->willReturn($this->websiteId);

        $this->controller = new ExportController(
            $this->mockContext,
            $this->mockProductExportHelper,
            $this->mockRequestBuilder,
            $this->mockProductRepository,
            $this->mockStoreManager
        );
    }

    public function testExecute(): void
    {
        $this->mockProductExportHelper
            ->expects($this->once())
            ->method('isEnabled')
            ->with(null)
            ->willReturn(true);

        $this->mockProduct
            ->expects($this->once())
            ->method('getTypeId')
            ->willReturn('simple');

        $this->mockProduct
            ->expects($this->atLeast(1))
            ->method('getSku')
            ->willReturn($this->productSku);

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('setRequestData')
            ->with(
                'Export',
                'Product',
                'Create'
            );

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('setScopeId')
            ->with($this->websiteId);

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('addRequestLine')
            ->with(json_encode([
                'sku' => $this->productSku,
            ]));

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('saveRequest')
            ->willReturn($this->mockRequestModel);

        $this->mockObjectManager
            ->expects($this->once())
            ->method('create')
            ->with('ProductCreateRequestProcessor')
            ->willReturn($this->mockRequestProcessor);

        $this->mockRequestProcessor
            ->expects($this->once())
            ->method('process')
            ->with($this->mockRequestModel)
            ->willReturn($this->mockExport);

        $this->mockResultRedirect
            ->expects($this->once())
            ->method('setRefererUrl')
            ->willReturnSelf();

        $result = $this->controller->execute();
        $this->assertEquals($this->mockResultRedirect, $result);
    }

    /**
     * @dataProvider executeGetProductExceptions
     * @param \Throwable $e
     * @return void
     */
    public function testExecuteGetProductExceptions(\Throwable $e): void
    {
        $this->mockRequest
            ->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn($this->productId);

        $this->mockProductExportHelper
            ->expects($this->once())
            ->method('isEnabled')
            ->with(null)
            ->willReturn(true);

        $this->mockProductRepository
            ->expects($this->once())
            ->method('getById')
            ->with($this->productId)
            ->willThrowException($e);

        $this->mockMessageManager
            ->expects($this->once())
            ->method('addError')
            ->with(__('Product Not Found.'));

        $this->mockActionFlag
            ->expects($this->once())
            ->method('set')
            ->with('', ExportController::FLAG_NO_DISPATCH, true);

        $this->mockResultRedirect
            ->expects($this->once())
            ->method('setRefererUrl')
            ->willReturnSelf();

        $result = $this->controller->execute();
        $this->assertEquals($this->mockResultRedirect, $result);
    }

    public function testExecuteProductExportDisabled(): void
    {
        $this->mockProductExportHelper
            ->expects($this->once())
            ->method('isEnabled')
            ->with(null)
            ->willReturn(false);

        $this->mockMessageManager
            ->expects($this->once())
            ->method('addError')
            ->with(__('Product exports are currently disabled. Please review the OrderFlow module configuration.'));

        $this->mockResultRedirect
            ->expects($this->once())
            ->method('setRefererUrl')
            ->willReturnSelf();

        $result = $this->controller->execute();
        $this->assertEquals($this->mockResultRedirect, $result);
    }

    public function testExecuteNonSimpleProduct(): void
    {
        $this->mockProductExportHelper
            ->expects($this->once())
            ->method('isEnabled')
            ->with(null)
            ->willReturn(true);

        $this->mockProduct
            ->expects($this->once())
            ->method('getTypeId')
            ->willReturn('configurable');

        $this->mockMessageManager
            ->expects($this->once())
            ->method('addError')
            ->with(__('This product cannot be exported. OrderFlow only supports simple product types.'));

        $this->mockResultRedirect
            ->expects($this->once())
            ->method('setRefererUrl')
            ->willReturnSelf();

        $result = $this->controller->execute();
        $this->assertEquals($this->mockResultRedirect, $result);
    }

    /**
     * @dataProvider executeGetExportFailureExceptions
     * @return void
     */
    public function testExecuteExportFailures(int $duplicates, int $failures): void
    {
        $this->mockProductExportHelper
            ->expects($this->once())
            ->method('isEnabled')
            ->with(null)
            ->willReturn(true);

        $this->mockProduct
            ->expects($this->once())
            ->method('getTypeId')
            ->willReturn('simple');

        $this->mockProduct
            ->expects($this->atLeast(1))
            ->method('getSku')
            ->willReturn($this->productSku);

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('setRequestData')
            ->with(
                'Export',
                'Product',
                'Create'
            );

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('setScopeId')
            ->with($this->websiteId);

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('addRequestLine')
            ->with(json_encode([
                'sku' => $this->productSku,
            ]));

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('saveRequest')
            ->willReturn($this->mockRequestModel);

        $this->mockObjectManager
            ->expects($this->once())
            ->method('create')
            ->with('ProductCreateRequestProcessor')
            ->willReturn($this->mockRequestProcessor);

        $this->mockRequestProcessor
            ->expects($this->once())
            ->method('process')
            ->with($this->mockRequestModel)
            ->willReturn($this->mockExport);

        $this->mockExport
            ->method('getDuplicates')
            ->willReturn($duplicates);

        $this->mockExport
            ->expects($this->once())
            ->method('getFailures')
            ->willReturn($failures);

        $this->mockMessageManager
            ->expects($this->once())
            ->method('addError')
            ->with(__("Product $this->productSku has failed to be queued for export to OrderFlow."));

        $this->mockResultRedirect
            ->expects($this->once())
            ->method('setRefererUrl')
            ->willReturnSelf();

        $result = $this->controller->execute();
        $this->assertEquals($this->mockResultRedirect, $result);
    }

    public function testExecuteGeneralException(): void
    {
        $this->mockProductExportHelper
            ->expects($this->once())
            ->method('isEnabled')
            ->with(null)
            ->willReturn(true);

        $this->mockProductRepository
            ->expects($this->once())
            ->method('getById')
            ->with($this->productId)
            ->willThrowException(new \Exception('An error occurred.'));

        $this->mockMessageManager
            ->expects($this->once())
            ->method('addError')
            ->with('An error occurred.');

        $this->mockResultRedirect
            ->expects($this->once())
            ->method('setRefererUrl')
            ->willReturnSelf();

        $result = $this->controller->execute();
        $this->assertEquals($this->mockResultRedirect, $result);
    }

    public function executeGetExportFailureExceptions(): array
    {
        return [
            [1, 0],
            [0, 1],
        ];
    }

    public function executeGetProductExceptions(): array
    {
        return [
            [new \Magento\Framework\Exception\NoSuchEntityException()],
            [new \Magento\Framework\Exception\InputException()],
        ];
    }
}