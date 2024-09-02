<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Ui\Component\Listing\Columns;

use Magento\Framework\Stdlib\BooleanUtils;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use RealtimeDespatch\OrderFlow\Ui\Component\Listing\Columns\Exported;
use RealtimeDespatch\OrderFlow\Ui\Component\Listing\Columns\UnitsActiveQuotes;
use RealtimeDespatch\OrderFlow\Ui\Component\Listing\Columns\UnitsUnsentOrders;

/**
 * Class ExportedTest
 * @package RealtimeDespatch\OrderFlow\Test\Unit\Ui\Component\Listing\Columns
 */
class ExportedTest extends \PHPUnit\Framework\TestCase
{
    protected Exported $component;
    protected ContextInterface $mockContext;
    protected UiComponentFactory $mockUiComponentFactory;
    protected TimezoneInterface $mockTimezone;
    protected BooleanUtils $mockBooleanUtils;

    protected function setUp(): void
    {
        $this->mockContext = $this->createMock(ContextInterface::class);
        $this->mockUiComponentFactory = $this->createMock(UiComponentFactory::class);
        $this->mockTimezone = $this->createMock(TimezoneInterface::class);
        $this->mockBooleanUtils = $this->createMock(BooleanUtils::class);

        $this->component = new Exported(
            $this->mockContext,
            $this->mockUiComponentFactory,
            $this->mockTimezone,
            $this->mockBooleanUtils,
            [],
            ['name' => 'exported'],
        );
    }

    public function testPrepareDataSource(): void
    {
        $result = $this->component->prepareDataSource([
            'data' => [
                'items' => [
                    []
                ]
            ]
        ]);

        $this->assertEquals(__('Pending'), $result['data']['items'][0]['exported']);
    }
}