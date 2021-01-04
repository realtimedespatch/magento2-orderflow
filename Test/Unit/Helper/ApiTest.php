<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Helper\Api;

class ApiTest extends TestCase
{
    /**
     * @var MockObject
     */
    protected $scopeConfigMock;

    /**
     * @var Api
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

        $this->helper = new Api($context);
    }

    /**
     * @dataProvider dataProviderGetEndpoint
     * @param $configPath
     * @param $configValue
     * @param $returnValue
     * @param $scopeId
     */
    public function testGetEndpoint($configPath, $configValue, $returnValue, $scopeId)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->with($configPath, ScopeInterface::SCOPE_WEBSITE, $scopeId)
            ->willReturn($configValue);

        $this->assertEquals($returnValue, $this->helper->getEndpoint($scopeId));
    }

    /**
     * @return array
     */
    public function dataProviderGetEndpoint()
    {
        return [
            ['orderflow_api/settings/endpoint', null, null, 1],
            ['orderflow_api/settings/endpoint', 'https://www.test-endpoint.com/', 'https://www.test-endpoint.com/', 1],
        ];
    }

    /**
     * @dataProvider dataProviderGetUsername
     * @param $configPath
     * @param $configValue
     * @param $returnValue
     * @param $scopeId
     */
    public function testGetUsername($configPath, $configValue, $returnValue, $scopeId)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->with($configPath, ScopeInterface::SCOPE_WEBSITE, $scopeId)
            ->willReturn($configValue);

        $this->assertEquals($returnValue, $this->helper->getUsername($scopeId));
    }

    /**
     * @return array
     */
    public function dataProviderGetUsername()
    {
        return [
            ['orderflow_api/settings/username', null, null, 1],
            ['orderflow_api/settings/username', 'orderflow', 'orderflow', 1],
        ];
    }

    /**
     * @dataProvider dataProviderGetPassword
     * @param $configPath
     * @param $configValue
     * @param $returnValue
     * @param $scopeId
     */
    public function testGetPassword($configPath, $configValue, $returnValue, $scopeId)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->with($configPath, ScopeInterface::SCOPE_WEBSITE, $scopeId)
            ->willReturn($configValue);

        $this->assertEquals($returnValue, $this->helper->getPassword($scopeId));
    }

    /**
     * @return array
     */
    public function dataProviderGetPassword()
    {
        return [
            ['orderflow_api/settings/password', null, null, 1],
            ['orderflow_api/settings/password', 'xb1723sha', 'xb1723sha', 1],
        ];
    }

    /**
     * @dataProvider dataProviderGetOrganisation
     * @param $configPath
     * @param $configValue
     * @param $returnValue
     * @param $scopeId
     */
    public function testGetOrganisation($configPath, $configValue, $returnValue, $scopeId)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->with($configPath, ScopeInterface::SCOPE_WEBSITE, $scopeId)
            ->willReturn($configValue);

        $this->assertEquals($returnValue, $this->helper->getOrganisation($scopeId));
    }

    /**
     * @return array
     */
    public function dataProviderGetOrganisation()
    {
        return [
            ['orderflow_api/settings/organisation', null, null, 1],
            ['orderflow_api/settings/organisation', 'Acme', 'Acme', 1],
        ];
    }

    /**
     * @dataProvider dataProviderGetChannel
     * @param $configPath
     * @param $configValue
     * @param $returnValue
     * @param $scopeId
     */
    public function testGetChannel($configPath, $configValue, $returnValue, $scopeId)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->with($configPath, ScopeInterface::SCOPE_WEBSITE, $scopeId)
            ->willReturn($configValue);

        $this->assertEquals($returnValue, $this->helper->getChannel($scopeId));
    }

    /**
     * @return array
     */
    public function dataProviderGetChannel()
    {
        return [
            ['orderflow_api/settings/channel', null, null, 1],
            ['orderflow_api/settings/channel', 'Test Channel', 'Test Channel', 1],
        ];
    }

    public function testGetCredentials()
    {
        $scopeId = 1;
        $endpoint = 'orderflow_api/settings/endpoint';
        $username = 'username';
        $password = 'password';
        $organisation = 'organisation';
        $channel = 'channel';

        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->will($this->onConsecutiveCalls(
                $endpoint,
                $username,
                $password,
                $organisation,
                $channel)
            );

        $result = $this->helper->getCredentials($scopeId);

        $this->assertInstanceOf('SixBySix\RealtimeDespatch\Api\Credentials', $result);
        $this->assertEquals($endpoint, $result->getEndpoint());
        $this->assertEquals($username, $result->getUsername());
        $this->assertEquals($password, $result->getPassword());
        $this->assertEquals($organisation, $result->getOrganisation());
        $this->assertEquals($channel, $result->getChannel());
    }
}
