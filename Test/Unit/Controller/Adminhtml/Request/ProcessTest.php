<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Controller\Adminhtml\Request;

use Magento\Backend\Model\View\Result\Redirect;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface as HttpRequestInterface;
use Magento\Framework\Message\ManagerInterface;
use RealtimeDespatch\OrderFlow\Controller\Adminhtml\Request\Process;
use Magento\Backend\App\Action\Context;
use \RealtimeDespatch\OrderFlow\Api\RequestRepositoryInterface;
use RealtimeDespatch\OrderFlow\Model\Request;
use RealtimeDespatch\OrderFlow\Model\Service\Request\RequestProcessor;


/**
 * Class ProcessTest
 * @package RealtimeDespatch\OrderFlow\Test\Unit\Controller\Adminhtml\Request
 */
class ProcessTest extends \PHPUnit\Framework\TestCase
{
    protected Process $controller;
    protected Context $mockContext;
    protected RequestRepositoryInterface $mockRequestRepository;
    protected Request $mockRequest;
    protected HttpRequestInterface $mockHttpRequest;
    protected RedirectFactory $mockRedirectFactory;
    protected Redirect $mockRedirect;
    protected ManagerInterface $mockMessageManager;
    protected ObjectManager $mockObjectManager;
    protected RequestProcessor $mockRequestProcessor;

    protected int $requestId = 1;

    protected function setUp(): void
    {
        $this->mockContext = $this->createMock(Context::class);
        $this->mockRequestRepository = $this->createMock(RequestRepositoryInterface::class);
        $this->mockHttpRequest = $this->createMock(HttpRequestInterface::class);
        $this->mockRedirectFactory = $this->createMock(RedirectFactory::class);
        $this->mockRedirect = $this->createMock(Redirect::class);
        $this->mockMessageManager = $this->createMock(ManagerInterface::class);
        $this->mockRequest = $this->createMock(Request::class);
        $this->mockObjectManager = $this->createMock(ObjectManager::class);
        $this->mockRequestProcessor = $this->createMock(RequestProcessor::class);

        $this->mockContext
            ->expects($this->once())
            ->method('getRequest')
            ->willReturn($this->mockHttpRequest);

        $this->mockContext
            ->expects($this->once())
            ->method('getResultRedirectFactory')
            ->willReturn($this->mockRedirectFactory);

        $this->mockContext
            ->expects($this->once())
            ->method('getMessageManager')
            ->willReturn($this->mockMessageManager);

        $this->mockContext
            ->expects($this->once())
            ->method('getObjectManager')
            ->willReturn($this->mockObjectManager);

        $this->mockRedirectFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->mockRedirect);

        $this->mockRedirect
            ->expects($this->once())
            ->method('setRefererUrl')
            ->willReturnSelf();

        $this->mockObjectManager
            ->method('create')
            ->willReturn($this->mockRequestProcessor);

        $this->mockRequest
            ->method('getEntity')
            ->willReturn('Product');

        $this->mockRequest
            ->method('getOperation')
            ->willReturn('Create');

        $this->controller = new Process(
            $this->mockContext,
            $this->mockRequestRepository
        );
    }

    public function testExecute(): void
    {
        $this->mockHttpRequest
            ->expects($this->once())
            ->method('getParam')
            ->with('request_id')
            ->willReturn($this->requestId);

        $this->mockRequestRepository
            ->expects($this->once())
            ->method('get')
            ->with($this->requestId)
            ->willReturn($this->mockRequest);

        $this->mockRequestProcessor
            ->expects($this->once())
            ->method('process')
            ->with($this->mockRequest);

        $this->mockMessageManager
            ->expects($this->once())
            ->method('addSuccess')
            ->with(__('The request has been processed.'));

        $result = $this->controller->execute();
        $this->assertEquals($this->mockRedirect, $result);
    }

    public function testExecuteNoId(): void
    {
        $this->mockMessageManager
            ->expects($this->once())
            ->method('addError')
            ->with(__('Request Not Found'));

        $result = $this->controller->execute();
        $this->assertEquals($this->mockRedirect, $result);
    }

    public function testExecuteException(): void
    {
        $this->mockHttpRequest
            ->expects($this->once())
            ->method('getParam')
            ->with('request_id')
            ->willReturn($this->requestId);

        $this->mockRequestRepository
            ->expects($this->once())
            ->method('get')
            ->with($this->requestId)
            ->willThrowException(new \Exception('Test Exception'));

        $this->mockMessageManager
            ->expects($this->once())
            ->method('addError')
            ->with(__('Test Exception'));

        $result = $this->controller->execute();
        $this->assertEquals($this->mockRedirect, $result);
    }
}