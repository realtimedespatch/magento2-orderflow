<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Plugin\Adminhtml;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\AuthorizationInterface;
use RealtimeDespatch\OrderFlow\Plugin\Adminhtml\ProductActions;
use Magento\Catalog\Ui\Component\Listing\Columns\ProductActions as Actions;

class ProductActionsTest extends TestCase
{
    /**
     * @var ContextInterface|MockObject
     */
    protected $context;

    /**
     * @var UrlInterface|MockObject
     */
    protected $url;

    /**
     * @var AuthorizationInterface|MockObject
     */
    protected $auth;

    /**
     * @var Actions|MockObject
     */
    protected $viewAction;

    /**
     * @var ProductActions
     */
    protected $plugin;

    public function setUp()
    {
        $this->context = $this->getMockBuilder(ContextInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->url = $this->getMockBuilder(UrlInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->auth = $this->getMockBuilder(AuthorizationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->viewAction = $this->getMockBuilder(Actions::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->plugin = new ProductActions(
            $this->context,
            $this->url,
            $this->auth
        );
    }

    /**
     * @param $dataSource
     * @param $entityId
     * @param $storeId
     * @param $isAllowed
     * @param $actionName
     * @param $exportUrl
     * @param $expectedResult
     * @dataProvider dataProviderAfterPrepareDataSource
     */
    public function testAfterPrepareDataSource(
        $dataSource,
        $entityId,
        $storeId,
        $isAllowed,
        $actionName,
        $exportUrl,
        $expectedResult
    ) {
        $this->context->expects($this->any())
            ->method('getFilterParam')
            ->with($this->equalTo('store_id'))
            ->willReturn($storeId);

        $this->auth->expects($this->any())
            ->method('isAllowed')
            ->willReturn($isAllowed);

        $this->viewAction->expects($this->any())
            ->method('getData')
            ->with($this->equalTo('name'))
            ->willReturn($actionName);

        $this->url->expects($this->any())
            ->method('getUrl')
            ->with('orderflow/product/export', ['id' => $entityId, 'store' => $storeId])
            ->willReturn($exportUrl);

        $this->assertEquals($expectedResult, $this->plugin->afterPrepareDataSource($this->viewAction, $dataSource));
    }

    public function dataProviderAfterPrepareDataSource()
    {
        $entityId = 666;
        $exportUrl = 'http://www.example.com/orderflow/product/export/'.$entityId;
        $viewUrl = 'http://www.example.com/orderflow/product/view/'.$entityId;

        $dataSource = [
            'data' => [
                'items' => [
                    [
                        'entity_id' => $entityId,
                        'action' => [
                            'view' => [
                                'href' => $viewUrl,
                                'label' => __('View')
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return [
            [
                [],
                $entityId,
                1,
                true,
                'action',
                $exportUrl,
                []
            ],
            [
                $dataSource,
                $entityId,
                1,
                false,
                'action',
                $exportUrl,
                $dataSource
            ],
            [
                $dataSource,
                $entityId,
                1,
                true,
                'action',
                $exportUrl,
                [
                    'data' => [
                        'items' => [
                            [
                                'entity_id' => $entityId,
                                'action' => [
                                    'view' => [
                                        'href' => $viewUrl,
                                        'label' => __('View')
                                    ],
                                    'export' => [
                                        'href' => $exportUrl,
                                        'label' => __('Export')
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];
    }
}
