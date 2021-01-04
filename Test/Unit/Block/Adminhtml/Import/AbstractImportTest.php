<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Import;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\RequestInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Api\Data\ImportInterface;
use RealtimeDespatch\OrderFlow\Api\ImportRepositoryInterface;
use RealtimeDespatch\OrderFlow\Block\Adminhtml\Import\AbstractImport;

class AbstractImportTest extends TestCase
{
    /**
     * @var MockObject
     */
    protected $request;

    /**
     * @var MockObject
     */
    protected $importRepository;

    /**
     * @var AbstractImport|__anonymous@925
     */
    protected $block;

    public function setUp()
    {
        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->request = $this->getMockBuilder(RequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->importRepository = $this->getMockBuilder(ImportRepositoryInterface::class)
            ->getMock();

        $data = [];

        $this->block = new class ($context, $this->request, $this->importRepository, $data) extends AbstractImport {};
    }

    public function testSetAndGetImportWithValidImport()
    {
        $import = $this->getMockBuilder(ImportInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->block->setImport($import);

        $this->assertSame($import, $this->block->getImport());
    }

    public function testSetAndGetImportWithNullImport()
    {
        $importId = 1;

        $import = $this->getMockBuilder(ImportInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->request->expects($this->once())
            ->method('getParam')
            ->with('import_id')
            ->willReturn($importId);

        $this->importRepository->expects($this->once())
            ->method('get')
            ->with($importId)
            ->willReturn($import);

        $this->assertSame($import, $this->block->getImport());
    }
}
