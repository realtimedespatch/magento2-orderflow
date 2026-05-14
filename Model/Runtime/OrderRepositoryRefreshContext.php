<?php
declare(strict_types=1);

namespace RealtimeDespatch\OrderFlow\Model\Runtime;

class OrderRepositoryRefreshContext
{
    private ?int $forcedOrderId = null;
    private bool $guardActive = false;

    public function getForcedOrderId(): ?int
    {
        return $this->forcedOrderId;
    }

    public function setGuardActive(bool $guardActive): void
    {
        $this->guardActive = $guardActive;
    }

    public function isGuardActive(): bool
    {
        return $this->guardActive;
    }

    /**
     * Run a callback while forcing a fresh repository read for a specific order.
     *
     * @param int $orderId
     * @param callable $callback
     * @return mixed
     */
    public function runForOrderId(int $orderId, callable $callback)
    {
        $previousOrderId = $this->forcedOrderId;
        $this->forcedOrderId = $orderId;

        try {
            return $callback();
        } finally {
            $this->forcedOrderId = $previousOrderId;
        }
    }
}
