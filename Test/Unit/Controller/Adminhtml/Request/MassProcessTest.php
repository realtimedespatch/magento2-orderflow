<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Controller\Adminhtml\Request;

use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\Result\RedirectFactory;
use RealtimeDespatch\OrderFlow\Controller\Adminhtml\Request\MassProcess;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Controller\Adminhtml\Product\Builder;
use Magento\Ui\Component\MassAction\Filter;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Request\CollectionFactory;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Request\Collection;
use Magento\Framework\DB\Transaction;
use Magento\Framework\Message\ManagerInterface;
use RealtimeDespatch\OrderFlow\Model\Service\Request\RequestProcessor;
use RealtimeDespatch\OrderFlow\Model\Request;

/**
 * Class MassResetTest
 * @package RealtimeDespatch\OrderFlow\Test\Unit\Controller\Adminhtml\Request
 */
class MassProcessTest extends \PHPUnit\Framework\TestCase
{
    protected MassProcess $controller;
    protected Context $mockContext;
    protected Builder $mockBuilder;
    protected Filter $mockFilter;
    protected CollectionFactory $mockCollectionFactory;
    protected Collection $mockCollection;
    protected RedirectFactory $mockRedirectFactory;
    protected Redirect $mockRedirect;
    protected ManagerInterface $mockMessageManager;
    protected ObjectManager $mockObjectManager;
    protected RequestProcessor $mockRequestProcessor;

    protected function setUp(): void
    {
        $this->mockContext = $this->createMock(Context::class);
        $this->mockBuilder = $this->createMock(Builder::class);
        $this->mockFilter = $this->createMock(Filter::class);
        $this->mockCollectionFactory = $this->createMock(CollectionFactory::class);
        $this->mockCollection = $this->createMock(Collection::class);
        $this->mockObjectManager = $this->createMock(ObjectManager::class);

        $this->mockMessageManager = $this->createMock(ManagerInterface::class);
        $this->mockRedirectFactory = $this->createMock(RedirectFactory::class);
        $this->mockRedirect = $this->createMock(Redirect::class);
        $this->mockRequestProcessor = $this->createMock(RequestProcessor::class);

        $this->mockCollectionFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->mockCollection);

        $this->mockFilter
            ->expects($this->once())
            ->method('getCollection')
            ->willReturn($this->mockCollection);

        $this->mockContext
            ->expects($this->once())
            ->method('getMessageManager')
            ->willReturn($this->mockMessageManager);

        $this->mockContext
            ->expects($this->once())
            ->method('getObjectManager')
            ->willReturn($this->mockObjectManager);

        $this->mockContext
            ->expects($this->once())
            ->method('getResultRedirectFactory')
            ->willReturn($this->mockRedirectFactory);

        $this->mockRedirectFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->mockRedirect);

        $this->controller = new MassProcess(
            $this->mockContext,
            $this->mockFilter,
            $this->mockCollectionFactory,
        );
    }

    public function testExecute(): void
    {
        $numRequests = rand(1, 10);

        $mockRequests = array_map(function() {

            $r = $this->createMock(Request::class);

            $r->expects($this->once())
                ->method('getEntity')
                ->willReturn('Product');

            $r->expects($this->once())
                ->method('getOperation')
                ->willReturn('Export');

            return $r;

        }, range(1, $numRequests));

        $this->mockCollection
            ->expects($this->once())
            ->method('getSize')
            ->willReturn($numRequests);

        $this->mockCollection
            ->expects($this->once())
            ->method('getItems')
            ->willReturn($mockRequests);

        $this->mockObjectManager
            ->expects($this->exactly($numRequests))
            ->method('create')
            ->with('ProductExportRequestProcessor')
            ->willReturn($this->mockRequestProcessor);

        $this->mockRequestProcessor
            ->expects($this->exactly($numRequests))
            ->method('process');

        $this->mockRedirect
            ->expects($this->once())
            ->method('setRefererUrl')
            ->willReturnSelf();

        $result = $this->controller->execute();
        $this->assertEquals($this->mockRedirect, $result);
    }
}