<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Controller\Adminhtml\Import;

use Magento\Framework\Registry;
use RealtimeDespatch\OrderFlow\Controller\Adminhtml\Import\Index as ImportIndexController;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Translate\InlineInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Framework\Controller\Result\RawFactory;
use RealtimeDespatch\OrderFlow\Api\ImportRepositoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Framework\View\Page\Title as PageTitle;
use Magento\Framework\View\LayoutInterface;

/**
 * Class IndexTest
 * @package RealtimeDespatch\OrderFlow\Test\Unit\Controller\Adminhtml\Import
 */
class IndexTest extends \PHPUnit\Framework\TestCase
{
    protected ImportIndexController $controller;
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
    protected ImportRepositoryInterface $mockImportRepository;
    protected LoggerInterface $mockLogger;
    protected RequestInterface $mockRequest;
    protected LayoutInterface $mockLayout;

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
        $this->mockImportRepository = $this->createMock(ImportRepositoryInterface::class);
        $this->mockLogger = $this->createMock(LoggerInterface::class);
        $this->mockRequest = $this->createMock(RequestInterface::class);
        $this->mockLayout = $this->createMock(LayoutInterface::class);

        $this->mockContext
            ->method('getRequest')
            ->willReturn($this->mockRequest);

        $this->mockResultPageFactory
            ->method('create')
            ->willReturn($this->mockResultPage);

        $this->mockResultPage
            ->method('getConfig')
            ->willReturn($this->mockPageConfig);

        $this->mockPageConfig
            ->method('getTitle')
            ->willReturn($this->mockPageTitle);

        $this->controller = new ImportIndexController(
            $this->mockContext,
            $this->mockRegistry,
            $this->mockFileFactory,
            $this->mockTranslateInline,
            $this->mockResultPageFactory,
            $this->mockResultJsonFactory,
            $this->mockResultLayoutFactory,
            $this->mockResultRawFactory,
            $this->mockImportRepository,
            $this->mockLogger
        );
    }

    /**
     * @dataProvider executeDataProvider
     * @param string $importType
     * @param string $unsetLayoutHandle
     * @return void
     */
    public function testExecute(string $importType, string $unsetLayoutHandle): void
    {
        $this->mockPageTitle
            ->expects($this->once())
            ->method('prepend')
            ->with(__("$importType Imports"));

        $this->mockResultPage
            ->expects($this->once())
            ->method('addBreadcrumb')
            ->with(__($importType), __($importType));

        $this->mockResultPage
            ->expects($this->once())
            ->method('getLayout')
            ->willReturn($this->mockLayout);

        $this->mockLayout
            ->expects($this->once())
            ->method('unsetChild')
            ->with('content', $unsetLayoutHandle);

        $this->mockRequest
            ->method('getParam')
            ->with('type')
            ->willReturn($importType);

        $result = $this->controller->execute();
        $this->assertEquals($this->mockResultPage, $result);
    }

    public function executeDataProvider(): array
    {
        return [
            ['Inventory', 'shipment_import_listing'],
            ['Shipment', 'inventory_import_listing'],
        ];
    }
}