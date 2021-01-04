<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Import\View\Tab;

use Magento\Framework\App\RequestInterface;
use Magento\Backend\Block\Template\Context;
use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Api\ImportRepositoryInterface;
use RealtimeDespatch\OrderFlow\Block\Adminhtml\Import\View\Tab\Info;

class InfoTest extends TestCase
{
    /**
     * @var Info
     */
    protected $block;

    public function setUp()
    {
        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $request = $this->getMockBuilder(RequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $importRepository = $this->getMockBuilder(ImportRepositoryInterface::class)
            ->getMock();

        $data = [];

        $this->block = new Info(
            $context,
            $request,
            $importRepository,
            $data
        );
    }

    public function testGetTabLabel()
    {
        $expectedLabel = __('Information');

        $this->assertEquals($expectedLabel, $this->block->getTabLabel());
    }

    public function testGetTabTitle()
    {
        $expectedTitle = __('Information');

        $this->assertEquals($expectedTitle, $this->block->getTabTitle());
    }

    public function testCanShowTab()
    {
        $this->assertTrue($this->block->canShowTab());
    }

    public function testIsHidden()
    {
        $this->assertFalse($this->block->isHidden());
    }

    public function testIsAjaxLoaded()
    {
        $this->assertFalse($this->block->isAjaxLoaded());
    }
}
