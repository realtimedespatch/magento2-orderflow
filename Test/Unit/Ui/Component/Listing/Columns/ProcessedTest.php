<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Ui\Component\Listing\Columns;

use Magento\Framework\Stdlib\BooleanUtils;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use RealtimeDespatch\OrderFlow\Ui\Component\Listing\Columns\Processed;

/**
 * Class ProcessedTest
 * @package RealtimeDespatch\OrderFlow\Test\Unit\Ui\Component\Listing\Columns
 */
class ProcessedTest extends \PHPUnit\Framework\TestCase
{
    protected Processed $component;
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

        $this->component = new Processed(
            $this->mockContext,
            $this->mockUiComponentFactory,
            $this->mockTimezone,
            $this->mockBooleanUtils,
            [],
            [
                'name' => 'processed',
                'config' => [
                    'timezone' => false,
                ]
            ],
        );
    }

    public function testPrepareDataSource(): void
    {
        $inputDate = '2024-08-24 13:05:47';

        $this->mockTimezone
            ->expects($this->once())
            ->method('date')
            ->with(new \DateTime($inputDate))
            ->willReturn(new \DateTime($inputDate));

        $result = $this->component->prepareDataSource([
            'data' => [
                'items' => [
                    ['processed' => $inputDate],
                ]
            ]
        ]);

        $this->assertEquals($inputDate, $result['data']['items'][0]['processed']);
    }

    public function testPrepareDataSourcePending(): void
    {
        $result = $this->component->prepareDataSource([
            'data' => [
                'items' => [
                    []
                ]
            ]
        ]);

        $this->assertEquals(__('Pending'), $result['data']['items'][0]['processed']);
    }
}