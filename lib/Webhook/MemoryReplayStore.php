<?php

declare(strict_types=1);

// Source: signwell-sdk-generator/extras/php/overlay/lib/Webhook/MemoryReplayStore.php
// Do not edit the generated SDK copy directly.

namespace SignWell\Sdk\Webhook;

final class MemoryReplayStore implements ReplayStoreInterface
{
    /** @var array<string, float> */
    private array $entries = [];

    /** @param callable|null $now */
    public function __construct(
        private readonly int $maxEntries = 10000,
        private $now = null
    ) {
        if ($maxEntries <= 0) {
            throw new \InvalidArgumentException('maxEntries must be a positive integer');
        }
    }

    public function add(string $key, float $expiresAtUnixSeconds): bool
    {
        $currentTime = $this->currentTime();
        foreach ($this->entries as $entryKey => $expiresAt) {
            if ($expiresAt < $currentTime) {
                unset($this->entries[$entryKey]);
            }
        }

        if (array_key_exists($key, $this->entries)) {
            return false;
        }

        if (count($this->entries) >= $this->maxEntries) {
            throw new ReplayStoreCapacityExceededException('webhook replay store capacity was exceeded');
        }

        $this->entries[$key] = $expiresAtUnixSeconds;

        return true;
    }

    private function currentTime(): float
    {
        if ($this->now !== null) {
            return (float) ($this->now)();
        }

        return (float) time();
    }
}
