<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Request;

use Magento\Framework\Exception\LocalizedException;
use RealtimeDespatch\OrderFlow\Api\Data\RequestInterface;

/**
 * Class AbstractRequest
 * @package RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Request
 */
abstract class AbstractRequest extends \PHPUnit\Framework\TestCase
{
    protected \RealtimeDespatch\OrderFlow\Block\Adminhtml\Request\AbstractRequest $block;
    protected \Magento\Framework\Registry $mockRegistry;
    protected RequestInterface $mockRequest;

    /**
     * @dataProvider getRequestDataProvider
     * @param bool $setData
     * @param ?string $registryKey
     * @return void
     */
    public function testGetRequest(bool $setData, ?string $registryKey): void
    {
        $this->block->unsetData('request');
        if ($setData) {
            $this->block->setData('request', $this->mockRequest);
        } else if (!is_null($registryKey)) {
            $this->mockRegistry
                ->method('registry')
                ->willReturnCallback(function ($key) use ($registryKey) {
                    if ($key === $registryKey) {
                        return $this->mockRequest;
                    }
                    return null;
                });
        } else {
            $this->expectException(LocalizedException::class);
            $this->expectExceptionMessage('Request Not Found.');
        }

        $result = $this->block->getRequest();
        $this->assertEquals($this->mockRequest, $result);
    }

    public function getRequestDataProvider(): array
    {
        return [
            [true, null],
            [false, 'current_request'],
            [false, 'request'],
            [false, null],
        ];
    }
}