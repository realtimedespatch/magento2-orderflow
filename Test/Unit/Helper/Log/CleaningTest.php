<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Helper\Log;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Helper\Log\Cleaning;
use Magento\Framework\Stdlib\DateTime\DateTime;

class CleaningTest extends TestCase
{
    /**
     * @var MockObject
     */
    protected $scopeConfigMock;

    /**
     * @var MockObject
     */
    protected $dateTimeMock;

    /**
     * @var Cleaning
     */
    protected $helper;

    public function setUp()
    {
        $this->scopeConfigMock = $this->getMockBuilder(ScopeConfigInterface::class)
            ->getMock();

        $this->dateTimeMock = $this->getMockBuilder(DateTime::class)
            ->disableOriginalConstructor()
            ->getMock();

        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $context->expects($this->once())
            ->method('getScopeConfig')
            ->willReturn($this->scopeConfigMock);

        $this->helper = new Cleaning($context, $this->dateTimeMock);
    }

    /**
     * @dataProvider dataProviderIsEnabled
     * @param $configPath
     * @param $configValue
     * @param $returnValue
     */
    public function testIsEnabled($configPath, $configValue, $returnValue)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('isSetFlag')
            ->with($configPath, ScopeInterface::SCOPE_WEBSITE)
            ->willReturn($configValue);

        $this->assertEquals($returnValue, $this->helper->isEnabled());
    }

    /**
     * @return array
     */
    public function dataProviderIsEnabled()
    {
        return [
            ['orderflow_log_cleaning/settings/is_enabled', true, true],
            ['orderflow_log_cleaning/settings/is_enabled', false, false],
        ];
    }

    /**
     * @dataProvider dataProviderGetExportLogDuration
     * @param $configPath
     * @param $configValue
     * @param $returnValue
     */
    public function testGetExportLogDuration($configPath, $configValue, $returnValue)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->with($configPath, ScopeInterface::SCOPE_WEBSITE)
            ->willReturn($configValue);

        $this->assertEquals($returnValue, $this->helper->getExportLogDuration());
    }

    /**
     * @return array
     */
    public function dataProviderGetExportLogDuration()
    {
        return [
            ['orderflow_log_cleaning/settings/export_duration', null, 1],
            ['orderflow_log_cleaning/settings/export_duration', false, 1],
            ['orderflow_log_cleaning/settings/export_duration', 10, 10],
        ];
    }

    /**
     * @dataProvider dataProviderGetImportLogDuration
     * @param $configPath
     * @param $configValue
     * @param $returnValue
     */
    public function testGetImportLogDuration($configPath, $configValue, $returnValue)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->with($configPath, ScopeInterface::SCOPE_WEBSITE)
            ->willReturn($configValue);

        $this->assertEquals($returnValue, $this->helper->getImportLogDuration());
    }

    /**
     * @return array
     */
    public function dataProviderGetImportLogDuration()
    {
        return [
            ['orderflow_log_cleaning/settings/import_duration', null, 1],
            ['orderflow_log_cleaning/settings/import_duration', false, 1],
            ['orderflow_log_cleaning/settings/import_duration', 10, 10],
        ];
    }

    /**
     * @dataProvider dataProviderGetRequestLogDuration
     * @param $configPath
     * @param $configValue
     * @param $returnValue
     */
    public function testGetRequestLogDuration($configPath, $configValue, $returnValue)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->with($configPath, ScopeInterface::SCOPE_WEBSITE)
            ->willReturn($configValue);

        $this->assertEquals($returnValue, $this->helper->getRequestLogDuration());
    }

    /**
     * @return array
     */
    public function dataProviderGetRequestLogDuration()
    {
        return [
            ['orderflow_log_cleaning/settings/request_duration', null, 1],
            ['orderflow_log_cleaning/settings/request_duration', false, 1],
            ['orderflow_log_cleaning/settings/request_duration', 10, 10],
        ];
    }

    /**
     * @dataProvider dataProviderGetExportLogExpirationDate
     * @param $configPath
     * @param $configValue
     * @param $dateFormat
     * @param $dateInput
     * @param $formattedDate
     * @param $expectedValue
     * @depends testGetExportLogDuration
     */
    public function testGetExportLogExpirationDate(
        $configPath,
        $configValue,
        $dateFormat,
        $dateInput,
        $formattedDate,
        $expectedValue
    ) {
        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->with($configPath, ScopeInterface::SCOPE_WEBSITE)
            ->willReturn($configValue);

        $this->dateTimeMock->expects($this->once())
            ->method('date')
            ->with($dateFormat, $dateInput)
            ->willReturn($formattedDate);

        $this->assertEquals($expectedValue, $this->helper->getExportLogExpirationDate());
    }

    /**
     * @return array
     */
    public function dataProviderGetExportLogExpirationDate()
    {
        return [
            [
                'orderflow_log_cleaning/settings/export_duration',
                10,
                'Y-m-d',
                '-9 days',
                '2020-11-30',
                '2020-11-30'
            ],
        ];
    }

    /**
     * @dataProvider dataProviderGetImportLogExpirationDate
     * @param $configPath
     * @param $configValue
     * @param $dateFormat
     * @param $dateInput
     * @param $formattedDate
     * @param $expectedValue
     * @depends testGetImportLogDuration
     */
    public function testGetImportLogExpirationDate(
        $configPath,
        $configValue,
        $dateFormat,
        $dateInput,
        $formattedDate,
        $expectedValue
    ) {
        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->with($configPath, ScopeInterface::SCOPE_WEBSITE)
            ->willReturn($configValue);

        $this->dateTimeMock->expects($this->once())
            ->method('date')
            ->with($dateFormat, $dateInput)
            ->willReturn($formattedDate);

        $this->assertEquals($expectedValue, $this->helper->getImportLogExpirationDate());
    }

    /**
     * @return array
     */
    public function dataProviderGetImportLogExpirationDate()
    {
        return [
            [
                'orderflow_log_cleaning/settings/import_duration',
                10,
                'Y-m-d',
                '-9 days',
                '2020-11-30',
                '2020-11-30'
            ],
        ];
    }

    /**
     * @dataProvider dataProviderGetRequestLogExpirationDate
     * @param $configPath
     * @param $configValue
     * @param $dateFormat
     * @param $dateInput
     * @param $formattedDate
     * @param $expectedValue
     * @depends testGetRequestLogDuration
     */
    public function testGetRequestLogExpirationDate(
        $configPath,
        $configValue,
        $dateFormat,
        $dateInput,
        $formattedDate,
        $expectedValue
    ) {
        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->with($configPath, ScopeInterface::SCOPE_WEBSITE)
            ->willReturn($configValue);

        $this->dateTimeMock->expects($this->once())
            ->method('date')
            ->with($dateFormat, $dateInput)
            ->willReturn($formattedDate);

        $this->assertEquals($expectedValue, $this->helper->getRequestLogExpirationDate());
    }

    /**
     * @return array
     */
    public function dataProviderGetRequestLogExpirationDate()
    {
        return [
            [
                'orderflow_log_cleaning/settings/request_duration',
                10,
                'Y-m-d',
                '-9 days',
                '2020-11-30',
                '2020-11-30'
            ],
        ];
    }
}
