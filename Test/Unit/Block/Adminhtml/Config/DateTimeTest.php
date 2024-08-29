<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Config;

use RealtimeDespatch\OrderFlow\Block\Adminhtml\Config\DateTime;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class DateTimeTest
 * @package RealtimeDespatch\OrderFlow\Test\Unit\Block\Adminhtml\Config
 */
class DateTimeTest extends \PHPUnit\Framework\TestCase
{
    protected DateTime $block;
    protected Context $context;
    protected AbstractElement $element;

    protected function setUp(): void
    {
        $this->context = $this->createMock(Context::class);

        $this->element = $this->getMockForAbstractClass(
            originalClassName: AbstractElement::class,
            arguments: [
                'factoryElement' => $this->createMock(\Magento\Framework\Data\Form\Element\Factory::class),
                'factoryCollection' => $this->createMock(\Magento\Framework\Data\Form\Element\CollectionFactory::class),
                'escaper' => $this->createMock(\Magento\Framework\Escaper::class),
            ],
            mockedMethods: ['setDateFormat', 'setTimeFormat', 'getForm'],
        );

        $this->element
            ->method('getForm')
            ->willReturn($this->createMock(\Magento\Framework\Data\Form::class));

        $this->block = new DateTime(
            $this->context
        );
    }

    public function testRender(): void
    {
        $this->element
            ->expects($this->once())
            ->method('setDateFormat')
            ->willReturn('Y-m-d H:i:s');

        $this->element
            ->expects($this->once())
            ->method('setTimeFormat')
            ->willReturn('H:i:s');

        $this->block->render($this->element);
    }
}