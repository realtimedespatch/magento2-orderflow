<?php
declare(strict_types=1);

namespace Magento\Sales\Api\Data {
    if (!class_exists(OrderExtensionFactory::class, false)) {
        class OrderExtensionFactory
        {
            private object $extensionAttributes;

            public function __construct(object $extensionAttributes)
            {
                $this->extensionAttributes = $extensionAttributes;
            }

            public function create(array $data = []): object
            {
                return $this->extensionAttributes;
            }
        }
    }

    if (!interface_exists(OrderExtensionInterface::class, false)) {
        interface OrderExtensionInterface
        {
        }
    }

    if (!interface_exists(OrderInterface::class, false)) {
        interface OrderInterface
        {
            public function getExtensionAttributes();
            public function getData($key = '', $index = null);
            public function setExtensionAttributes($extensionAttributes);
        }
    }

    if (!interface_exists(OrderSearchResultInterface::class, false)) {
        interface OrderSearchResultInterface
        {
            public function getItems();
        }
    }
}

namespace Magento\Sales\Api {
    if (!interface_exists(OrderRepositoryInterface::class, false)) {
        interface OrderRepositoryInterface
        {
        }
    }
}

namespace RealtimeDespatch\OrderFlow\Test\Unit\Plugin\Sales {

require_once dirname(__DIR__, 4) . '/Plugin/Sales/OrderRepository.php';

use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use RealtimeDespatch\OrderFlow\Plugin\Sales\OrderRepository;

class TestOrderExtensionAttributes implements \Magento\Sales\Api\Data\OrderExtensionInterface
{
    public mixed $orderflowExportDate = null;
    public mixed $orderflowExportStatus = null;

    public function setOrderflowExportDate(mixed $value): self
    {
        $this->orderflowExportDate = $value;

        return $this;
    }

    public function setOrderflowExportStatus(mixed $value): self
    {
        $this->orderflowExportStatus = $value;

        return $this;
    }
}

class TestOrder implements \Magento\Sales\Api\Data\OrderInterface
{
    public ?object $extensionAttributes;
    public array $data;
    public ?object $setExtensionAttributesArgument = null;

    public function __construct(array $data, ?object $extensionAttributes = null)
    {
        $this->data = $data;
        $this->extensionAttributes = $extensionAttributes;
    }

    public function getExtensionAttributes(): ?object
    {
        return $this->extensionAttributes;
    }

    public function getData($key = '', $index = null): mixed
    {
        return $this->data[$key] ?? null;
    }

    public function setExtensionAttributes($extensionAttributes): self
    {
        $this->setExtensionAttributesArgument = $extensionAttributes;
        $this->extensionAttributes = $extensionAttributes;

        return $this;
    }
}

class TestOrderSearchResult implements \Magento\Sales\Api\Data\OrderSearchResultInterface
{
    public function __construct(private array $items)
    {
    }

    public function getItems(): array
    {
        return $this->items;
    }
}

class TestOrderRepository implements OrderRepositoryInterface
{
}

class OrderRepositoryTest extends \PHPUnit\Framework\TestCase
{
    protected OrderRepository $plugin;
    protected OrderExtensionFactory $orderExtensionFactory;
    protected TestOrderExtensionAttributes $createdExtensionAttributes;

    protected function setUp(): void
    {
        $this->createdExtensionAttributes = new TestOrderExtensionAttributes();
        $this->orderExtensionFactory = new OrderExtensionFactory($this->createdExtensionAttributes);
        $this->plugin = new OrderRepository($this->orderExtensionFactory);
    }

    public function testAfterGetSetsOrderFlowExtensionAttributes(): void
    {
        $orderRepository = new TestOrderRepository();
        $order = new TestOrder([
            'orderflow_export_date' => '2026-04-22 10:00:00',
            'orderflow_export_status' => 'Exported',
        ]);

        $this->assertSame($order, $this->plugin->afterGet($orderRepository, $order));
        $this->assertSame($this->createdExtensionAttributes, $order->setExtensionAttributesArgument);
        $this->assertSame('2026-04-22 10:00:00', $this->createdExtensionAttributes->orderflowExportDate);
        $this->assertSame('Exported', $this->createdExtensionAttributes->orderflowExportStatus);
    }

    public function testAfterGetListSetsOrderFlowExtensionAttributesForEachOrder(): void
    {
        $orderRepository = new TestOrderRepository();
        $existingExtensionAttributes = new TestOrderExtensionAttributes();
        $orderOne = new TestOrder([
            'orderflow_export_date' => null,
            'orderflow_export_status' => 'Pending',
        ], $existingExtensionAttributes);
        $orderTwo = new TestOrder([
            'orderflow_export_date' => '2026-04-22 12:00:00',
            'orderflow_export_status' => 'Queued',
        ]);
        $searchResult = new TestOrderSearchResult([$orderOne, $orderTwo]);

        $this->assertSame($searchResult, $this->plugin->afterGetList($orderRepository, $searchResult));
        $this->assertSame($existingExtensionAttributes, $orderOne->setExtensionAttributesArgument);
        $this->assertNull($existingExtensionAttributes->orderflowExportDate);
        $this->assertSame('Pending', $existingExtensionAttributes->orderflowExportStatus);
        $this->assertSame($this->createdExtensionAttributes, $orderTwo->setExtensionAttributesArgument);
        $this->assertSame('2026-04-22 12:00:00', $this->createdExtensionAttributes->orderflowExportDate);
        $this->assertSame('Queued', $this->createdExtensionAttributes->orderflowExportStatus);
    }
}
}
