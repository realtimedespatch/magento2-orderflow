<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\System\Message\Export;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Notification\MessageInterface;
use Magento\Framework\UrlInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Api\Data\ExportInterface;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Export\Collection;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Export\CollectionFactory;
use RealtimeDespatch\OrderFlow\System\Message\Export\Failure;


class FailureTest extends TestCase
{
    /**
     * @var MockObject
     */
    protected $collectionFactory;

    /**
     * @var MockObject
     */
    protected $authorisation;

    /**
     * @var MockObject
     */
    protected $url;

    /**
     * @var Failure
     */
    protected $message;

    public function setUp()
    {
        $this->collectionFactory = $this->getMockBuilder(CollectionFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->authorisation = $this->getMockBuilder(AuthorizationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->url = $this->getMockBuilder(UrlInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->message = new Failure(
            $this->collectionFactory,
            $this->authorisation,
            $this->url
        );
    }

    public function testGetIdentity()
    {
        $this->assertEquals(Failure::IDENTITY, $this->message->getIdentity());
    }

    public function testGetSeverity()
    {
        $this->assertEquals( MessageInterface::SEVERITY_MINOR, $this->message->getSeverity());
    }

    public function testGetUnreadFailedExport()
    {
        $export = $this->getMockBuilder(ExportInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $exportCollection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $exportCollection->expects($this->atMost(1))
            ->method('getUnreadFailedExport')
            ->willReturn($export);

        $this->collectionFactory->expects($this->atMost(1))
            ->method('create')
            ->willReturn($exportCollection);

        $this->assertSame($export, $this->message->getUnreadFailedExport());
        $this->assertSame($export, $this->message->getUnreadFailedExport());
    }

    /**
     * @param $isAllowed
     * @param $exportId
     * @param $expectedResult
     * @depends testGetUnreadFailedExport
     * @dataProvider dataProviderIsDisplayable
     */
    public function testIsDisplayable(
        $isAllowed,
        $exportId,
        $expectedResult
    ) {
        $this->authorisation->expects($this->once())
            ->method('isAllowed')
            ->with(Failure::ACL_RESOURCE)
            ->willReturn($isAllowed);

        $export = $this->getMockBuilder(ExportInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $export->expects($this->atMost(1))
            ->method('getId')
            ->willReturn($exportId);

        $exportCollection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $exportCollection->expects($this->atMost(1))
            ->method('getUnreadFailedExport')
            ->willReturn($export);

        $this->collectionFactory->expects($this->atMost(1))
            ->method('create')
            ->willReturn($exportCollection);

        $this->assertEquals($expectedResult, $this->message->isDisplayed());
    }

    /**
     * @return array
     */
    public function dataProviderIsDisplayable()
    {
        return [
            [false, null, false],
            [false, false, false],
            [false, 666, false],
            [true, null, false],
            [true, false, false],
            [true, 666, true],
        ];
    }

    /**
     * @param $exportId
     * @param $url
     * @param $expectedResult
     * @dataProvider dataProviderGetText
     */
    public function testGetText(
        $exportId,
        $url,
        $expectedResult
    ) {
        $export = $this->getMockBuilder(ExportInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $export->expects($this->atMost(2))
            ->method('getId')
            ->willReturn($exportId);

        $exportCollection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $exportCollection->expects($this->atMost(1))
            ->method('getUnreadFailedExport')
            ->willReturn($export);

        $this->collectionFactory->expects($this->atMost(1))
            ->method('create')
            ->willReturn($exportCollection);

        $this->url->expects($this->any())
            ->method('getUrl')
            ->with(
                'orderflow/export/view',
                ['export_id' => $exportId]
            )
            ->willReturn($url);

        $this->assertEquals($expectedResult, $this->message->getText());
    }

    public function dataProviderGetText()
    {
        return [
            [
                666,
                'http://www.example.com/export/id/1',
                __('A recent OrderFlow export contains failures. <a href="http://www.example.com/export/id/1">View Details</a>')
            ],
            [
                false,
                'http://www.example.com/export/id/1',
                __('An unexpected error has occurred')
            ],
        ];
    }
}
