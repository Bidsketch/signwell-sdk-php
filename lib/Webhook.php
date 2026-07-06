<?php

declare(strict_types=1);

// Source: signwell-sdk-generator/extras/php/overlay/lib/Webhook.php
// Do not edit the generated SDK copy directly.

namespace SignWell\Sdk;

use SignWell\Sdk\Webhook\ReplayStoreInterface;

final class Webhook
{
    /**
     * @param array<string, mixed> $event
     * @param callable|null $now
     */
    public static function verifyEvent(
        array $event,
        string $webhookId,
        ?float $toleranceSeconds = null,
        ?callable $now = null
    ): bool {
        try {
            self::verifyEventOrThrow($event, $webhookId, $toleranceSeconds, $now);
        } catch (\InvalidArgumentException) {
            return false;
        }

        return true;
    }

    /**
     * @param array<string, mixed> $event
     * @param callable|null $now
     */
    public static function verifyEventOrThrow(
        array $event,
        string $webhookId,
        ?float $toleranceSeconds = null,
        ?callable $now = null
    ): bool {
        self::verifyEventData($event, $webhookId, $toleranceSeconds, $now);

        return true;
    }

    /**
     * @param array<string, mixed> $event
     * @param callable|null $now
     */
    public static function verifyEventOnce(
        array $event,
        string $webhookId,
        ReplayStoreInterface $replayStore,
        ?float $toleranceSeconds,
        ?callable $now = null
    ): bool {
        try {
            self::verifyEventOnceOrThrow($event, $webhookId, $replayStore, $toleranceSeconds, $now);
        } catch (\InvalidArgumentException) {
            return false;
        }

        return true;
    }

    /**
     * @param array<string, mixed> $event
     * @param callable|null $now
     */
    public static function verifyEventOnceOrThrow(
        array $event,
        string $webhookId,
        ReplayStoreInterface $replayStore,
        ?float $toleranceSeconds,
        ?callable $now = null
    ): bool {
        if ($toleranceSeconds === null) {
            throw new \InvalidArgumentException('toleranceSeconds is required for replay protection');
        }

        $parsed = self::verifyEventData($event, $webhookId, $toleranceSeconds, $now);
        $expiresAt = (float) $parsed['time'] + $toleranceSeconds;
        if (!$replayStore->add(self::replayKey($event), $expiresAt)) {
            throw new \InvalidArgumentException('webhook event has already been processed');
        }

        return true;
    }

    /** @param array<string, mixed> $event */
    public static function replayKey(array $event): string
    {
        $parsed = self::parseEvent($event);

        return sprintf('signwell:%s:%s:%s', (string) $parsed['type'], (string) $parsed['time'], $parsed['hash']);
    }

    /**
     * @param array<string, mixed> $event
     * @param callable|null $now
     * @return array{type:mixed,time:mixed,hash:string}
     */
    private static function verifyEventData(
        array $event,
        string $webhookId,
        ?float $toleranceSeconds,
        ?callable $now
    ): array {
        if ($webhookId === '') {
            throw new \InvalidArgumentException('webhookId must be a non-empty string');
        }

        $parsed = self::parseEvent($event);
        if ($toleranceSeconds !== null) {
            self::verifyFreshTimestamp($parsed['time'], $toleranceSeconds, $now);
        }

        $signedData = sprintf('%s@%s', (string) $parsed['type'], (string) $parsed['time']);
        $calculated = hash_hmac('sha256', $signedData, $webhookId);
        if (!hash_equals($calculated, $parsed['hash'])) {
            throw new \InvalidArgumentException('webhook signature is invalid');
        }

        return $parsed;
    }

    /**
     * @param array<string, mixed> $event
     * @return array{type:mixed,time:mixed,hash:string}
     */
    private static function parseEvent(array $event): array
    {
        $type = $event['type'] ?? null;
        $time = $event['time'] ?? null;
        $hash = $event['hash'] ?? null;
        $missing = [];

        foreach (['type' => $type, 'time' => $time, 'hash' => $hash] as $key => $value) {
            if ($value === null || $value === '') {
                $missing[] = $key;
            }
        }

        if ($missing !== []) {
            throw new \InvalidArgumentException(
                'event is missing required keys: ' . implode(', ', $missing) . '. Make sure you pass payload["event"], not the full webhook payload'
            );
        }

        if (!is_string($hash)) {
            throw new \InvalidArgumentException('event.hash must be a string');
        }

        return ['type' => $type, 'time' => $time, 'hash' => $hash];
    }

    /** @param callable|null $now */
    private static function verifyFreshTimestamp(mixed $eventTime, float $toleranceSeconds, ?callable $now): void
    {
        if (!is_finite($toleranceSeconds) || $toleranceSeconds < 0) {
            throw new \InvalidArgumentException('toleranceSeconds must be a non-negative number');
        }

        if (!is_numeric($eventTime)) {
            throw new \InvalidArgumentException('event.time must be a Unix timestamp when toleranceSeconds is provided');
        }

        $currentTime = $now === null ? (float) time() : (float) $now();
        $timestamp = (float) $eventTime;
        if (!is_finite($timestamp) || abs($currentTime - $timestamp) > $toleranceSeconds) {
            throw new \InvalidArgumentException('webhook timestamp is outside the allowed tolerance');
        }
    }
}
