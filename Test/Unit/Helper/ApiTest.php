<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use RealtimeDespatch\OrderFlow\Helper\Api;
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    protected Api $api;
    protected ScopeConfigInterface $mockScopeConfig;

    protected function setUp(): void
    {
        $this->mockScopeConfig = $this->createMock(ScopeConfigInterface::class);
        $mockContext = $this->createMock(\Magento\Framework\App\Helper\Context::class);
        $mockContext->method('getScopeConfig')->willReturn($this->mockScopeConfig);

        $this->api = new Api(
            $mockContext,
        );
    }


    public function testGetEndpoint()
    {
        $this->mockScopeConfig
            ->expects($this->once())
            ->method('getValue')
            ->with(
                'orderflow_api/settings/endpoint',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
                null
            )
            ->willReturn('http://test.com');

        $this->assertEquals(
            'http://test.com',
            $this->api->getEndpoint()
        );
    }

    public function testGetUsername()
    {
        $this->mockScopeConfig
            ->expects($this->once())
            ->method('getValue')
            ->with(
                'orderflow_api/settings/username',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
                null
            )
            ->willReturn('demo_user');

        $this->assertEquals(
            'demo_user',
            $this->api->getUsername()
        );
    }

    public function testGetPassword()
    {
        $this->mockScopeConfig
            ->expects($this->once())
            ->method('getValue')
            ->with(
                'orderflow_api/settings/password',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
                null
            )
            ->willReturn('demo_password');

        $this->assertEquals(
            'demo_password',
            $this->api->getPassword()
        );
    }

    public function testGetOrganisation()
    {
        $this->mockScopeConfig
            ->expects($this->once())
            ->method('getValue')
            ->with(
                'orderflow_api/settings/organisation',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
                null
            )
            ->willReturn('demo_org');

        $this->assertEquals(
            'demo_org',
            $this->api->getOrganisation()
        );
    }

    public function testGetChannel()
    {
        $this->mockScopeConfig
            ->expects($this->once())
            ->method('getValue')
            ->with(
                'orderflow_api/settings/channel',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
                null
            )
            ->willReturn('demo_channel');

        $this->assertEquals(
            'demo_channel',
            $this->api->getChannel()
        );
    }

    public function testGetCredentials()
    {
        $this->mockScopeConfig
            ->expects($this->exactly(5))
            ->method('getValue')
            ->withConsecutive(
                [
                    'orderflow_api/settings/endpoint',
                    \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
                    null
                ],
                [
                    'orderflow_api/settings/username',
                    \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
                    null
                ],
                [
                    'orderflow_api/settings/password',
                    \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
                    null
                ],
                [
                    'orderflow_api/settings/organisation',
                    \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
                    null
                ],
                [
                    'orderflow_api/settings/channel',
                    \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
                    null
                ]
            )
            ->willReturnOnConsecutiveCalls(
                'http://test.com',
                'demo_user',
                'demo_password',
                'demo_org',
                'demo_channel'
            );

        $credentials = $this->api->getCredentials();
        $this->assertEquals('http://test.com', $credentials->getEndpoint());
        $this->assertEquals('demo_user', $credentials->getUsername());
        $this->assertEquals('demo_password', $credentials->getPassword());
        $this->assertEquals('demo_org', $credentials->getOrganisation());
        $this->assertEquals('demo_channel', $credentials->getChannel());
    }
}
