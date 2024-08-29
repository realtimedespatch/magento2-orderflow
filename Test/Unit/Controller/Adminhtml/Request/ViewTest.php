<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Controller\Adminhtml\Request;

use Magento\Framework\App\ActionFlag;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Registry;
use RealtimeDespatch\OrderFlow\Api\RequestRepositoryInterface;
use RealtimeDespatch\OrderFlow\Controller\Adminhtml\Request\View as RequestViewController;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Translate\InlineInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\Result\Redirect;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Framework\View\Page\Title as PageTitle;
use Magento\Framework\View\LayoutInterface;
use RealtimeDespatch\OrderFlow\Model\Request;

/**
 * Class IndexTest
 * @package RealtimeDespatch\OrderFlow\Test\Unit\Controller\Adminhtml\Request
 */
class ViewTest extends \PHPUnit\Framework\TestCase
{
    protected RequestViewController $controller;
    protected Context $mockContext;
    protected Registry $mockRegistry;
    protected FileFactory $mockFileFactory;
    protected InlineInterface $mockTranslateInline;
    protected PageFactory $mockResultPageFactory;
    protected Page $mockResultPage;
    protected PageConfig $mockPageConfig;
    protected PageTitle $mockPageTitle;
    protected JsonFactory $mockResultJsonFactory;
    protected LayoutFactory $mockResultLayoutFactory;
    protected RawFactory $mockResultRawFactory;
    protected RequestRepositoryInterface $mockRequestRepository;
    protected LoggerInterface $mockLogger;
    protected RequestInterface $mockHttpRequest;
    protected LayoutInterface $mockLayout;
    protected RedirectFactory $mockResultRedirectFactory;
    protected Redirect $mockResultRedirect;
    protected ManagerInterface $mockMessageManager;
    protected ActionFlag $mockActionFlag;
    protected Request $mockRequest;

    protected function setUp(): void
    {
        $this->mockContext = $this->createMock(Context::class);
        $this->mockRegistry = $this->createMock(Registry::class);
        $this->mockFileFactory = $this->createMock(FileFactory::class);
        $this->mockTranslateInline = $this->createMock(InlineInterface::class);
        $this->mockResultPageFactory = $this->createMock(PageFactory::class);
        $this->mockResultPage = $this->createMock(Page::class);
        $this->mockPageConfig = $this->createMock(PageConfig::class);
        $this->mockPageTitle = $this->createMock(PageTitle::class);
        $this->mockResultJsonFactory = $this->createMock(JsonFactory::class);
        $this->mockResultLayoutFactory = $this->createMock(LayoutFactory::class);
        $this->mockResultRawFactory = $this->createMock(RawFactory::class);
        $this->mockRequestRepository = $this->createMock(RequestRepositoryInterface::class);
        $this->mockLogger = $this->createMock(LoggerInterface::class);
        $this->mockHttpRequest = $this->createMock(RequestInterface::class);
        $this->mockLayout = $this->createMock(LayoutInterface::class);
        $this->mockResultRedirectFactory = $this->createMock(RedirectFactory::class);
        $this->mockResultRedirect = $this->createMock(Redirect::class);
        $this->mockMessageManager = $this->createMock(ManagerInterface::class);
        $this->mockActionFlag = $this->createMock(ActionFlag::class);
        $this->mockRequest = $this->createMock(Request::class);

        $this->mockContext
            ->method('getRequest')
            ->willReturn($this->mockHttpRequest);

        $this->mockResultRedirectFactory
            ->method('create')
            ->willReturn($this->mockResultRedirect);

        $this->mockContext
            ->method('getResultRedirectFactory')
            ->willReturn($this->mockResultRedirectFactory);

        $this->mockContext
            ->method('getMessageManager')
            ->willReturn($this->mockMessageManager);

        $this->mockContext
            ->method('getActionFlag')
            ->willReturn($this->mockActionFlag);

        $this->mockResultPageFactory
            ->method('create')
            ->willReturn($this->mockResultPage);

        $this->mockResultPage
            ->method('getConfig')
            ->willReturn($this->mockPageConfig);

        $this->mockPageConfig
            ->method('getTitle')
            ->willReturn($this->mockPageTitle);

        $this->controller = new RequestViewController(
            $this->mockContext,
            $this->mockRegistry,
            $this->mockFileFactory,
            $this->mockTranslateInline,
            $this->mockResultPageFactory,
            $this->mockResultJsonFactory,
            $this->mockResultLayoutFactory,
            $this->mockResultRawFactory,
            $this->mockRequestRepository,
            $this->mockLogger
        );
    }

    /**
     * @dataProvider executeNoImportDataProvider
     * @param \Throwable $e
     * @return void
     */
    public function testExecuteNoImport(\Throwable $e): void
    {
        $this->mockHttpRequest
            ->expects($this->once())
            ->method('getParam')
            ->with('request_id')
            ->willReturn(1);

        $this->mockRequestRepository
            ->expects($this->once())
            ->method('get')
            ->with(1)
            ->willThrowException($e);

        $this->mockMessageManager
            ->expects($this->once())
            ->method('addError')
            ->with(__('This request no longer exists.'));

        $this->mockActionFlag
            ->expects($this->once())
            ->method('set')
            ->with('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);

        $this->mockResultRedirect
            ->expects($this->once())
            ->method('setRefererUrl')
            ->willReturnSelf();

        $result = $this->controller->execute();
        $this->assertEquals($this->mockResultRedirect, $result);
    }

    public function testExecuteException(): void
    {
        $exception = new \Exception("Test exception");

        $this->mockHttpRequest
            ->expects($this->once())
            ->method('getParam')
            ->with('request_id')
            ->willReturn(1);

        $this->mockRequestRepository
            ->expects($this->once())
            ->method('get')
            ->with(1)
            ->willReturn($this->mockRequest);

        $this->mockResultPageFactory
            ->method('create')
            ->willThrowException($exception);

        $this->mockLogger
            ->expects($this->once())
            ->method('critical')
            ->with($exception);

        $this->mockMessageManager
            ->expects($this->once())
            ->method('addError')
            ->with(__('Exception occurred during request load'));

        $this->mockResultRedirect
            ->expects($this->once())
            ->method('setRefererUrl')
            ->willReturnSelf();

        $result = $this->controller->execute();
        $this->assertEquals($this->mockResultRedirect, $result);
    }

    /**
     * @dataProvider executeDataProvider
     * @param string $type
     * @param string $unsetLayoutHandle
     * @return void
     */
    public function testExecute(string $type, string $unsetLayoutHandle): void
    {
        $this->mockHttpRequest
            ->expects($this->once())
            ->method('getParam')
            ->with('request_id')
            ->willReturn(1);

        $this->mockRequestRepository
            ->expects($this->once())
            ->method('get')
            ->with(1)
            ->willReturn($this->mockRequest);

        $this->mockRequest
            ->expects($this->exactly(2))
            ->method('getType')
            ->willReturn($type);

        $this->mockRequest
            ->expects($this->once())
            ->method('getMessageId')
            ->willReturn(12345);

        $this->mockPageTitle
            ->expects($this->once())
            ->method('prepend')
            ->with("$type Request #12345");

        $this->mockResultPageFactory
            ->method('create')
            ->willReturn($this->mockResultPage);

        $this->mockResultPage
            ->expects($this->once())
            ->method('addBreadcrumb')
            ->with(__('Requests'), __('Requests'))
            ->willReturn($this->mockResultPage);

        $this->mockResultPage
            ->expects($this->once())
            ->method('getLayout')
            ->willReturn($this->mockLayout);

        $this->mockLayout
            ->expects($this->once())
            ->method('unsetChild')
            ->with('lines', $unsetLayoutHandle);


        $result = $this->controller->execute();
        $this->assertEquals($this->mockResultPage, $result);
    }

    public function executeNoImportDataProvider(): array
    {
        return [
            [new NoSuchEntityException()],
            [new InputException()],
        ];
    }

    public function executeDataProvider(): array
    {
        return [
            ['Import', 'request_export_line_listing'],
            ['Export', 'request_import_line_listing'],
        ];
    }
}