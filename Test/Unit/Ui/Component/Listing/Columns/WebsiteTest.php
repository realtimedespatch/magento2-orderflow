<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\Website;
use Magento\Store\Model\WebsiteFactory;
use RealtimeDespatch\OrderFlow\Ui\Component\Listing\Columns\Website as WebsiteColumn;

/**
 * Class WebsiteTest
 * @package RealtimeDespatch\OrderFlow\Test\Unit\Ui\Component\Listing\Columns
 */
class WebsiteTest extends \PHPUnit\Framework\TestCase
{
    protected WebsiteColumn $column;
    protected ContextInterface $mockContext;
    protected UiComponentFactory $mockUiComponentFactory;
    protected WebsiteFactory $mockWebsiteFactory;
    protected Website $mockWebsite;

    protected function setUp(): void
    {
        $this->mockContext = $this->createMock(ContextInterface::class);
        $this->mockUiComponentFactory = $this->createMock(UiComponentFactory::class);
        $this->mockWebsiteFactory = $this->createMock(WebsiteFactory::class);
        $this->mockWebsite = $this->createMock(Website::class);

        $this->column = new WebsiteColumn(
            $this->mockContext,
            $this->mockUiComponentFactory,
            $this->mockWebsiteFactory,
            [],
            [
                'name' => 'website',
            ]
        );
    }

    public function testPrepareDataSourceScopeId(): void
    {
        $result = $this->column->prepareDataSource([
            'data' => [
                'items' => [
                    []
                ]
            ]
        ]);

        $this->assertEquals('OrderFlow', $result['data']['items'][0]['website']);
    }

    public function testPrepareDataSourceNoScopeId(): void
    {
        $this->mockWebsiteFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->mockWebsite);

        $this->mockWebsite
            ->expects($this->once())
            ->method('load')
            ->with(1)
            ->willReturnSelf();

        $this->mockWebsite
            ->expects($this->once())
            ->method('getName')
            ->willReturn('Website Name');

        $result = $this->column->prepareDataSource([
            'data' => [
                'items' => [
                    ['scope_id' => 1],
                ]
            ]
        ]);

        $this->assertEquals('Website Name', $result['data']['items'][0]['website']);
    }
}