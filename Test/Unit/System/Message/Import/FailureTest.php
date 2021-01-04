<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\System\Message\Import;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Notification\MessageInterface;
use Magento\Framework\UrlInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Api\Data\ImportInterface;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Import\Collection;
use RealtimeDespatch\OrderFlow\Model\ResourceModel\Import\CollectionFactory;
use RealtimeDespatch\OrderFlow\System\Message\Import\Failure;


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

    public function testGetUnreadFailedImport()
    {
        $import = $this->getMockBuilder(ImportInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $importCollection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $importCollection->expects($this->atMost(1))
            ->method('getUnreadFailedImport')
            ->willReturn($import);

        $this->collectionFactory->expects($this->atMost(1))
            ->method('create')
            ->willReturn($importCollection);

        $this->assertSame($import, $this->message->getUnreadFailedImport());
        $this->assertSame($import, $this->message->getUnreadFailedImport());
    }

    /**
     * @param $isAllowed
     * @param $importId
     * @param $expectedResult
     * @depends testGetUnreadFailedImport
     * @dataProvider dataProviderIsDisplayable
     */
    public function testIsDisplayable(
        $isAllowed,
        $importId,
        $expectedResult
    ) {
        $this->authorisation->expects($this->once())
            ->method('isAllowed')
            ->with(Failure::ACL_RESOURCE)
            ->willReturn($isAllowed);

        $import = $this->getMockBuilder(ImportInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $import->expects($this->atMost(1))
            ->method('getId')
            ->willReturn($importId);

        $importCollection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $importCollection->expects($this->atMost(1))
            ->method('getUnreadFailedImport')
            ->willReturn($import);

        $this->collectionFactory->expects($this->atMost(1))
            ->method('create')
            ->willReturn($importCollection);

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
     * @param $importId
     * @param $url
     * @param $expectedResult
     * @dataProvider dataProviderGetText
     */
    public function testGetText(
        $importId,
        $url,
        $expectedResult
    ) {
        $import = $this->getMockBuilder(ImportInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $import->expects($this->atMost(2))
            ->method('getId')
            ->willReturn($importId);

        $importCollection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $importCollection->expects($this->atMost(1))
            ->method('getUnreadFailedImport')
            ->willReturn($import);

        $this->collectionFactory->expects($this->atMost(1))
            ->method('create')
            ->willReturn($importCollection);

        $this->url->expects($this->any())
            ->method('getUrl')
            ->with(
                'orderflow/import/view',
                ['import_id' => $importId]
            )
            ->willReturn($url);

        $this->assertEquals($expectedResult, $this->message->getText());
    }

    public function dataProviderGetText()
    {
        return [
            [
                666,
                'http://www.example.com/import/id/1',
                __('A recent OrderFlow import contains failures. <a href="http://www.example.com/import/id/1">View Details</a>')
            ],
            [
                false,
                'http://www.example.com/import/id/1',
                __('An unexpected error has occurred')
            ],
        ];
    }
}
