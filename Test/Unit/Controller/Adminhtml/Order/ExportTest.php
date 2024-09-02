<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Controller\Adminhtml\Order;

use Magento\Backend\Model\View\Result\Redirect;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderRepository;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use RealtimeDespatch\OrderFlow\Controller\Adminhtml\Order\Export as ExportController;
use Magento\Backend\App\Action\Context;
use RealtimeDespatch\OrderFlow\Helper\Export\Order as OrderExportHelper;
use RealtimeDespatch\OrderFlow\Api\RequestBuilderInterface;
use RealtimeDespatch\OrderFlow\Model\Request as RequestModel;
use RealtimeDespatch\OrderFlow\Model\Export;
use Magento\Framework\App\ObjectManager;
use RealtimeDespatch\OrderFlow\Model\Builder\RequestBuilder;
use RealtimeDespatch\OrderFlow\Model\Service\Request\RequestProcessor;
use Magento\Framework\App\ActionFlag;

/**
 * Class ExportTest
 * @package RealtimeDespatch\OrderFlow\Test\Unit\Controller\Adminhtml\Order
 */
class ExportTest extends \PHPUnit\Framework\TestCase
{
    protected ExportController $controller;
    protected Context $mockContext;
    protected OrderExportHelper $mockOrderExportHelper;
    protected RequestBuilderInterface $mockRequestBuilder;
    protected OrderRepository $mockOrderRepository;
    protected StoreManagerInterface $mockStoreManager;
    protected RedirectFactory $mockResultRedirectFactory;
    protected Redirect $mockResultRedirect;
    protected RequestInterface $mockRequest;
    protected Order $mockOrder;
    protected Store $mockStore;
    protected ObjectManager $mockObjectManager;
    protected RequestProcessor $mockRequestProcessor;
    protected RequestModel $mockRequestModel;
    protected Export $mockExport;
    protected ManagerInterface $mockMessageManager;
    protected ActionFlag $mockActionFlag;

    protected int $orderId = 1;
    protected int $storeId = 2;
    protected int $websiteId = 3;
    protected string $orderIncrementId = '000000001';

    protected function setUp(): void
    {
        $this->mockContext = $this->createMock(Context::class);
        $this->mockOrderExportHelper = $this->createMock(OrderExportHelper::class);
        $this->mockRequestBuilder = $this->createMock(RequestBuilder::class);
        $this->mockOrderRepository = $this->createMock(OrderRepository::class);
        $this->mockStoreManager = $this->createMock(StoreManagerInterface::class);
        $this->mockResultRedirectFactory = $this->createMock(RedirectFactory::class);
        $this->mockResultRedirect = $this->createMock(Redirect::class);
        $this->mockRequest = $this->createMock(RequestInterface::class);
        $this->mockOrder = $this->createMock(Order::class);
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
            ->expects($this->once())
            ->method('getParam')
            ->with('order_id')
            ->willReturn($this->orderId);

        $this->mockOrderRepository
            ->expects($this->once())
            ->method('get')
            ->with($this->orderId)
            ->willReturn($this->mockOrder);

        $this->mockOrder
            ->method('getStoreId')
            ->willReturn($this->storeId);

        $this->mockOrder
            ->method('getEntityId')
            ->willReturn($this->orderId);

        $this->mockStoreManager
            ->method('getStore')
            ->with($this->storeId)
            ->willReturn($this->mockStore);

        $this->mockStore
            ->method('getWebsiteId')
            ->willReturn($this->websiteId);

        $this->controller = new ExportController(
            $this->mockContext,
            $this->mockOrderExportHelper,
            $this->mockRequestBuilder,
            $this->mockOrderRepository,
            $this->mockStoreManager
        );
    }

    public function testExecute(): void
    {
        $this->mockOrderExportHelper
            ->expects($this->once())
            ->method('isEnabled')
            ->with($this->websiteId)
            ->willReturn(true);

        $this->mockOrder
            ->expects($this->once())
            ->method('getIsVirtual')
            ->willReturn(false);

        $this->mockOrder
            ->method('getIncrementId')
            ->willReturn($this->orderIncrementId);

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('setRequestData')
            ->with(
                'Export',
                'Order',
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
                'entity_id' => $this->orderId,
                'increment_id' => $this->orderIncrementId,
            ]));

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('saveRequest')
            ->willReturn($this->mockRequestModel);

        $this->mockObjectManager
            ->expects($this->once())
            ->method('create')
            ->with('OrderCreateRequestProcessor')
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
     * @dataProvider executeGetOrderExceptions
     * @param \Throwable $e
     * @return void
     */
    public function testExecuteGetOrderExceptions(\Throwable $e): void
    {
        $this->mockRequest
            ->expects($this->once())
            ->method('getParam')
            ->with('order_id')
            ->willReturn($this->orderId);

        $this->mockOrderRepository
            ->expects($this->once())
            ->method('get')
            ->with($this->orderId)
            ->willThrowException($e);

        $this->mockMessageManager
            ->expects($this->once())
            ->method('addError')
            ->with(__('Order Not Found.'));

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

    public function testExecuteOrderExportDisabled(): void
    {
        $this->mockOrderExportHelper
            ->expects($this->once())
            ->method('isEnabled')
            ->with($this->websiteId)
            ->willReturn(false);

        $this->mockMessageManager
            ->expects($this->once())
            ->method('addError')
            ->with(__('Order exports are currently disabled. Please review the OrderFlow module configuration.'));

        $this->mockResultRedirect
            ->expects($this->once())
            ->method('setRefererUrl')
            ->willReturnSelf();

        $result = $this->controller->execute();
        $this->assertEquals($this->mockResultRedirect, $result);
    }

    public function testExecuteVirtualOrder(): void
    {
        $this->mockOrderExportHelper
            ->expects($this->once())
            ->method('isEnabled')
            ->with($this->websiteId)
            ->willReturn(true);

        $this->mockOrder
            ->expects($this->once())
            ->method('getIsVirtual')
            ->willReturn(true);

        $this->mockMessageManager
            ->expects($this->once())
            ->method('addError')
            ->with(__('You cannot export a virtual order to OrderFlow.'));

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
        $this->mockOrderExportHelper
            ->expects($this->once())
            ->method('isEnabled')
            ->with($this->websiteId)
            ->willReturn(true);

        $this->mockOrder
            ->method('getIncrementId')
            ->willReturn($this->orderIncrementId);

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('setRequestData')
            ->with(
                'Export',
                'Order',
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
                'entity_id' => $this->orderId,
                'increment_id' => $this->orderIncrementId,
            ]));

        $this->mockRequestBuilder
            ->expects($this->once())
            ->method('saveRequest')
            ->willReturn($this->mockRequestModel);

        $this->mockObjectManager
            ->expects($this->once())
            ->method('create')
            ->with('OrderCreateRequestProcessor')
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
            ->method('getFailures')
            ->willReturn($failures);

        $this->mockMessageManager
            ->method('addError')
            ->with(__("Order $this->orderIncrementId has failed to be queued for export to OrderFlow."));

        $this->mockResultRedirect
            ->expects($this->once())
            ->method('setRefererUrl')
            ->willReturnSelf();

        $result = $this->controller->execute();
        $this->assertEquals($this->mockResultRedirect, $result);
    }

    public function testExecuteGeneralException(): void
    {
        $this->mockOrderRepository
            ->expects($this->once())
            ->method('get')
            ->with($this->orderId)
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

    public function executeGetOrderExceptions(): array
    {
        return [
            [new \Magento\Framework\Exception\NoSuchEntityException()],
            [new \Magento\Framework\Exception\InputException()],
        ];
    }
}