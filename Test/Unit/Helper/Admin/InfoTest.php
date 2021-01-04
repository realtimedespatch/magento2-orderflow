<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Helper\Admin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Framework\App\Helper\Context;
use RealtimeDespatch\OrderFlow\Helper\Admin\Info;

class InfoTest extends TestCase
{
    /**
     * @var MockObject
     */
    protected $scopeConfigMock;

    /**
     * @var Info
     */
    protected $helper;

    public function setUp()
    {
        $this->scopeConfigMock = $this->getMockBuilder(ScopeConfigInterface::class)
            ->getMock();

        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $context->expects($this->once())
            ->method('getScopeConfig')
            ->willReturn($this->scopeConfigMock);

        $this->helper = new Info($context);
    }

    /**
     * @param boolean $isEnabled
     * @param boolean $expectedValue
     * @dataProvider dataProviderIsEnabled
     */
    public function testIsEnabled(bool $isEnabled, bool $expectedValue)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('isSetFlag')
            ->with('orderflow_admin_info/settings/is_enabled', ScopeInterface::SCOPE_WEBSITE)
            ->willReturn($isEnabled);

        $this->assertEquals($expectedValue, $this->helper->isEnabled());
    }

    /**
     * @return array
     */
    public function dataProviderIsEnabled()
    {
        return [
            [false, false],
            [true, true],
        ];
    }
}
