<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use RealtimeDespatch\OrderFlow\Ui\Component\Listing\Columns\UnitsActiveQuotes;
use RealtimeDespatch\OrderFlow\Ui\Component\Listing\Columns\UnitsUnsentOrders;

/**
 * Class UnitsUnsentOrdersTest
 * @package RealtimeDespatch\OrderFlow\Test\Unit\Ui\Component\Listing\Columns
 */
class UnitsActiveQuotesTest extends \PHPUnit\Framework\TestCase
{
    protected UnitsActiveQuotes $column;
    protected ContextInterface $mockContext;
    protected UiComponentFactory $mockUiComponentFactory;

    protected function setUp(): void
    {
        $this->mockContext = $this->createMock(ContextInterface::class);
        $this->mockUiComponentFactory = $this->createMock(UiComponentFactory::class);

        $this->column = new UnitsActiveQuotes(
            $this->mockContext,
            $this->mockUiComponentFactory,
            [],
            []
        );
    }

    public function testPrepareDataSource(): void
    {
        $result = $this->column->prepareDataSource([
            'data' => [
                'items' => [
                    ['additional_data' => '{"unitsActiveQuotes": 5}']
                ]
            ]
        ]);

        $this->assertEquals(5, $result['data']['items'][0]['active_quotes']);
    }
}