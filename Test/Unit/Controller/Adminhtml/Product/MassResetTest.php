<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Controller\Adminhtml\Product;

use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use RealtimeDespatch\OrderFlow\Controller\Adminhtml\Product\MassReset;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Controller\Adminhtml\Product\Builder;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\DB\Transaction;
use Magento\Framework\Message\ManagerInterface;

/**
 * Class MassResetTest
 * @package RealtimeDespatch\OrderFlow\Test\Unit\Controller\Adminhtml\Product
 */
class MassResetTest extends \PHPUnit\Framework\TestCase
{
    protected MassReset $controller;
    protected Context $mockContext;
    protected Builder $mockBuilder;
    protected Filter $mockFilter;
    protected CollectionFactory $mockCollectionFactory;
    protected Collection $mockCollection;
    protected Transaction $mockTransaction;
    protected RedirectFactory $mockRedirectFactory;
    protected Redirect $mockRedirect;
    protected ManagerInterface $mockMessageManager;

    protected function setUp(): void
    {
        $this->mockContext = $this->createMock(Context::class);
        $this->mockBuilder = $this->createMock(Builder::class);
        $this->mockFilter = $this->createMock(Filter::class);
        $this->mockCollectionFactory = $this->createMock(CollectionFactory::class);
        $this->mockCollection = $this->createMock(Collection::class);
        $this->mockTransaction = $this->createMock(Transaction::class);
        $this->mockMessageManager = $this->createMock(ManagerInterface::class);
        $this->mockRedirectFactory = $this->createMock(RedirectFactory::class);
        $this->mockRedirect = $this->createMock(Redirect::class);

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
            ->method('getResultRedirectFactory')
            ->willReturn($this->mockRedirectFactory);

        $this->mockRedirectFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->mockRedirect);

        $this->controller = new MassReset(
            $this->mockContext,
            $this->mockBuilder,
            $this->mockFilter,
            $this->mockCollectionFactory,
            $this->mockTransaction
        );
    }

    public function testExecute(): void
    {
        $numProducts = rand(1, 10);

        $mockProducts = array_map(function() {

            $p = $this->getMockBuilder(\Magento\Catalog\Model\Product::class)
                ->disableOriginalConstructor()
                ->addMethods(['setOrderflowExportStatus'])
                ->getMock();

            $p->expects($this->once())
                ->method('setOrderflowExportStatus')
                ->with('Pending')
                ->willReturnSelf();

            return $p;
        }, range(1, $numProducts));

        $this->mockCollection
            ->expects($this->once())
            ->method('getSize')
            ->willReturn($numProducts);

        $this->mockCollection
            ->expects($this->once())
            ->method('getItems')
            ->willReturn($mockProducts);

        $this->mockTransaction
            ->expects($this->exactly($numProducts))
            ->method('addObject');

        $this->mockTransaction
            ->expects($this->once())
            ->method('save');

        $this->mockRedirect
            ->expects($this->once())
            ->method('setRefererUrl')
            ->willReturnSelf();

        $result = $this->controller->execute();
        $this->assertEquals($this->mockRedirect, $result);
    }

    public function testExecuteWithException(): void
    {
        $numProducts = rand(1, 10);

        $mockProducts = array_map(function() {

            $p = $this->getMockBuilder(\Magento\Catalog\Model\Product::class)
                ->disableOriginalConstructor()
                ->addMethods(['setOrderflowExportStatus'])
                ->getMock();

            $p->expects($this->once())
                ->method('setOrderflowExportStatus')
                ->with('Pending')
                ->willReturnSelf();

            return $p;
        }, range(1, $numProducts));

        $this->mockCollection
            ->expects($this->once())
            ->method('getSize')
            ->willReturn($numProducts);

        $this->mockCollection
            ->expects($this->once())
            ->method('getItems')
            ->willReturn($mockProducts);

        $this->mockTransaction
            ->expects($this->exactly($numProducts))
            ->method('addObject');

        $this->mockTransaction
            ->expects($this->once())
            ->method('save')
            ->willThrowException(new \Exception('Test Exception'));

        $this->mockMessageManager
            ->expects($this->once())
            ->method('addError')
            ->with(__('OrderFlow Export Status reset failed: Test Exception'));

        $this->mockRedirect
            ->expects($this->once())
            ->method('setRefererUrl')
            ->willReturnSelf();

        $result = $this->controller->execute();
        $this->assertEquals($this->mockRedirect, $result);
    }
}