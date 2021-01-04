<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Plugin\Adminhtml;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Framework\AuthorizationInterface;
use Magento\Sales\Block\Adminhtml\Order\View;
use RealtimeDespatch\OrderFlow\Plugin\Adminhtml\OrderView;

class OrderViewTest extends TestCase
{
    /**
     * @var AuthorizationInterface|MockObject
     */
    protected $auth;

    /**
     * @var View|MockObject
     */
    protected $view;

    /**
     * @var OrderView
     */
    protected $plugin;

    public function setUp()
    {
        $this->auth = $this->getMockBuilder(AuthorizationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->view = $this->getMockBuilder(View::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->plugin = new OrderView($this->auth);
    }

    public function testBeforeSetLayoutWhenUserIsNotAuthorised()
    {
        $isAuthorised = false;

        $this->auth->expects($this->once())
            ->method('isAllowed')
            ->with(OrderView::ACL_RESOURCE)
            ->willReturn($isAuthorised);

        $this->view->expects($this->never())
            ->method('getUrl');

        $this->view->expects($this->never())
            ->method('addButton');

        $this->plugin->beforeSetLayout($this->view);
    }

    public function testBeforeSetLayoutWhenUserIsAuthorised()
    {
        $isAuthorised = true;
        $entityId = 666;
        $exportUrl = 'http://www.example.com/order/export/'.$entityId;
        $message = __('Are you sure you wish to export this order?');

        $this->auth->expects($this->once())
            ->method('isAllowed')
            ->with(OrderView::ACL_RESOURCE)
            ->willReturn($isAuthorised);

        $this->view->expects($this->once())
            ->method('getUrl')
            ->with(OrderView::EXPORT_URL_PATH)
            ->willReturn($exportUrl);

        $this->view->expects($this->once())
            ->method('addButton')
            ->with(
                'order_export',
                [
                    'label' => __('Export'),
                    'class' => 'export',
                    'id' => 'orderflow-order-view-export-button',
                    'onclick' => "confirmSetLocation('{$message}', '{$exportUrl}')"
                ]
            );

        $this->plugin->beforeSetLayout($this->view);
    }
}
