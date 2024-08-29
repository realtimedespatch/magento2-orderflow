<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Controller\Adminhtml\Export;

use Magento\Framework\App\ActionFlag;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Registry;
use RealtimeDespatch\OrderFlow\Controller\Adminhtml\Export\View as ExportViewController;
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
use RealtimeDespatch\OrderFlow\Api\ExportRepositoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Framework\View\Page\Title as PageTitle;
use Magento\Framework\View\LayoutInterface;
use RealtimeDespatch\OrderFlow\Model\Export;
use RealtimeDespatch\OrderFlow\Model\Import;

/**
 * Class IndexTest
 * @package RealtimeDespatch\OrderFlow\Test\Unit\Controller\Adminhtml\Export
 */
class ViewTest extends \PHPUnit\Framework\TestCase
{
    protected ExportViewController $controller;
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
    protected ExportRepositoryInterface $mockExportRepository;
    protected LoggerInterface $mockLogger;
    protected RequestInterface $mockRequest;
    protected LayoutInterface $mockLayout;
    protected RedirectFactory $mockResultRedirectFactory;
    protected Redirect $mockResultRedirect;
    protected ManagerInterface $mockMessageManager;
    protected ActionFlag $mockActionFlag;
    protected Export $mockExport;

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
        $this->mockExportRepository = $this->createMock(ExportRepositoryInterface::class);
        $this->mockLogger = $this->createMock(LoggerInterface::class);
        $this->mockRequest = $this->createMock(RequestInterface::class);
        $this->mockLayout = $this->createMock(LayoutInterface::class);
        $this->mockResultRedirectFactory = $this->createMock(RedirectFactory::class);
        $this->mockResultRedirect = $this->createMock(Redirect::class);
        $this->mockMessageManager = $this->createMock(ManagerInterface::class);
        $this->mockActionFlag = $this->createMock(ActionFlag::class);
        $this->mockExport = $this->createMock(Export::class);

        $this->mockContext
            ->method('getRequest')
            ->willReturn($this->mockRequest);

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

        $this->controller = new ExportViewController(
            $this->mockContext,
            $this->mockRegistry,
            $this->mockFileFactory,
            $this->mockTranslateInline,
            $this->mockResultPageFactory,
            $this->mockResultJsonFactory,
            $this->mockResultLayoutFactory,
            $this->mockResultRawFactory,
            $this->mockExportRepository,
            $this->mockLogger
        );
    }

    /**
     * @dataProvider executeNoExportDataProvider
     * @param \Throwable $e
     * @return void
     */
    public function testExecuteNoExport(\Throwable $e): void
    {
        $this->mockRequest
            ->expects($this->once())
            ->method('getParam')
            ->with('export_id')
            ->willReturn(1);

        $this->mockExportRepository
            ->expects($this->once())
            ->method('get')
            ->with(1)
            ->willThrowException($e);

        $this->mockMessageManager
            ->expects($this->once())
            ->method('addError')
            ->with(__('This export no longer exists.'));

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

        $this->mockRequest
            ->expects($this->once())
            ->method('getParam')
            ->with('export_id')
            ->willReturn(1);

        $this->mockExportRepository
            ->expects($this->once())
            ->method('get')
            ->with(1)
            ->willReturn($this->mockExport);

        $this->mockExport
            ->expects($this->once())
            ->method('setViewedAt')
            ->with(date('Y-m-d H:i:s'))
            ->willReturnSelf();

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
            ->with(__('Export Not Found'));

        $this->controller->execute();
    }

    /**
     * @dataProvider executeDataProvider
     * @param string $entity
     * @param string $unsetLayoutHandle
     * @return void
     */
    public function testExecute(string $entity, string $unsetLayoutHandle): void
    {
        $this->mockRequest
            ->expects($this->once())
            ->method('getParam')
            ->with('export_id')
            ->willReturn(1);

        $this->mockExportRepository
            ->expects($this->once())
            ->method('get')
            ->with(1)
            ->willReturn($this->mockExport);

        $this->mockExport
            ->expects($this->once())
            ->method('setViewedAt')
            ->with(date('Y-m-d H:i:s'))
            ->willReturnSelf();

        $this->mockExport
            ->expects($this->exactly(2))
            ->method('getEntity')
            ->willReturn($entity);

        $this->mockExport
            ->expects($this->once())
            ->method('getMessageId')
            ->willReturn(12345);

        $this->mockPageTitle
            ->expects($this->once())
            ->method('prepend')
            ->with("$entity Export #12345");

        $this->mockResultPageFactory
            ->method('create')
            ->willReturn($this->mockResultPage);

        $this->mockResultPage
            ->expects($this->once())
            ->method('addBreadcrumb')
            ->with(__('Exports'), __('Exports'));

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

    public function executeNoExportDataProvider(): array
    {
        return [
            [new NoSuchEntityException()],
            [new InputException()],
        ];
    }

    public function executeDataProvider(): array
    {
        return [
            ['Order', 'product_export_line_listing'],
            ['Product', 'order_export_line_listing'],
        ];
    }
}