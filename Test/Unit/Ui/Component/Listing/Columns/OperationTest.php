<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\Website;
use Magento\Store\Model\WebsiteFactory;
use RealtimeDespatch\OrderFlow\Ui\Component\Listing\Columns\Operation;

/**
 * Class OperationTest
 * @package RealtimeDespatch\OrderFlow\Test\Unit\Ui\Component\Listing\Columns
 */
class OperationTest extends \PHPUnit\Framework\TestCase
{
    protected Operation $column;
    protected ContextInterface $mockContext;
    protected UiComponentFactory $mockUiComponentFactory;

    protected function setUp(): void
    {
        $this->mockContext = $this->createMock(ContextInterface::class);
        $this->mockUiComponentFactory = $this->createMock(UiComponentFactory::class);

        $this->column = new Operation(
            $this->mockContext,
            $this->mockUiComponentFactory,
            [],
            [
                'name' => 'operation',
            ]
        );
    }

    public function testPrepareDataSourceCreate(): void
    {
        $result = $this->column->prepareDataSource([
            'data' => [
                'items' => [
                    ['operation' => 'Create'],
                ]
            ]
        ]);

        $this->assertEquals('Queue', $result['data']['items'][0]['operation']);
    }

    public function testPrepareDataSourceUpdate(): void
    {
        $result = $this->column->prepareDataSource([
            'data' => [
                'items' => [
                    ['operation' => 'Update'],
                ]
            ]
        ]);

        $this->assertEquals('Queue', $result['data']['items'][0]['operation']);
    }
}