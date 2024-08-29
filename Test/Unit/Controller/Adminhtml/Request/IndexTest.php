<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Controller\Adminhtml\Request;

use Magento\Framework\App\RequestInterface;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\View\Layout;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Title;
use PHPUnit\Framework\TestCase;

use RealtimeDespatch\OrderFlow\Controller\Adminhtml\Request\Index;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Translate\InlineInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Framework\Controller\Result\RawFactory;
use RealtimeDespatch\OrderFlow\Api\RequestRepositoryInterface;
use Psr\Log\LoggerInterface;

class IndexTest extends TestCase
{
    protected Index $controller;
    protected Context $mockContext;
    protected Registry $mockRegistry;
    protected FileFactory $mockFileFactory;
    protected InlineInterface $mockInlineInterface;
    protected PageFactory $mockPageFactory;
    protected Page $mockPage;
    protected JsonFactory $mockJsonFactory;
    protected LayoutFactory $mockLayoutFactory;
    protected RawFactory $mockRawFactory;
    protected RequestRepositoryInterface $mockRequestRepository;
    protected LoggerInterface $mockLogger;
    protected RequestInterface $mockRequest;
    protected Config $mockConfig;
    protected Title $mockTitle;
    protected Layout $mockLayout;

    protected function setUp(): void
    {
        $this->mockContext = $this->createMock(Context::class);
        $this->mockRegistry = $this->createMock(Registry::class);
        $this->mockFileFactory = $this->createMock(FileFactory::class);
        $this->mockInlineInterface = $this->createMock(InlineInterface::class);
        $this->mockPageFactory = $this->createMock(PageFactory::class);
        $this->mockJsonFactory = $this->createMock(JsonFactory::class);
        $this->mockLayoutFactory = $this->createMock(LayoutFactory::class);
        $this->mockRawFactory = $this->createMock(RawFactory::class);
        $this->mockRequestRepository = $this->createMock(RequestRepositoryInterface::class);
        $this->mockLogger = $this->createMock(LoggerInterface::class);

        $this->mockRequest = $this->createMock(RequestInterface::class);
        $this->mockPage = $this->createMock(Page::class);
        $this->mockConfig = $this->createMock(Config::class);
        $this->mockTitle = $this->createMock(Title::class);
        $this->mockLayout = $this->createMock(Layout::class);

        $this->mockContext
            ->method('getRequest')
            ->willReturn($this->mockRequest);

        $this->mockPageFactory
            ->method('create')
            ->willReturn($this->mockPage);

        $this->mockPage
            ->method('getConfig')
            ->willReturn($this->mockConfig);

        $this->mockConfig
            ->method('getTitle')
            ->willReturn($this->mockTitle);

        $this->mockPage
            ->method('getLayout')
            ->willReturn($this->mockLayout);

        $this->controller = new Index(
            $this->mockContext,
            $this->mockRegistry,
            $this->mockFileFactory,
            $this->mockInlineInterface,
            $this->mockPageFactory,
            $this->mockJsonFactory,
            $this->mockLayoutFactory,
            $this->mockRawFactory,
            $this->mockRequestRepository,
            $this->mockLogger
        );
    }

    /**
     * @dataProvider executeDataProvider
     * @param string $type
     * @param string $unsetLayoutHandle
     * @return void
     */
    public function testExecute(string $type, string $unsetLayoutHandle): void
    {
        $this->mockPage
            ->expects($this->once())
            ->method('addBreadcrumb')
            ->with(__('Requests'), __('Requests'));

        $this->mockRequest
            ->expects($this->once())
            ->method('getParam')
            ->with('type')
            ->willReturn($type);

        $this->mockLayout
            ->expects($this->once())
            ->method('unsetChild')
            ->with('content', $unsetLayoutHandle);

        $result = $this->controller->execute();
        $this->assertEquals($this->mockPage, $result);
    }

    public function executeDataProvider(): array
    {
        return [
            ['Import', 'export_request_listing'],
            ['Export', 'import_request_listing'],
        ];
    }
}