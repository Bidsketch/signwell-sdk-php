<?php

declare(strict_types=1);

namespace SignWell\Sdk\Test\Regression;

use PHPUnit\Framework\TestCase;
use SignWell\Sdk\Webhook;
use SignWell\Sdk\Webhook\MemoryReplayStore;

final class WebhookTest extends TestCase
{
    public function testVerifiesValidWebhookEvent(): void
    {
        $event = $this->signedEvent('whk_123');

        self::assertTrue(Webhook::verifyEvent($event, 'whk_123'));
        self::assertTrue(Webhook::verifyEventOrThrow($event, 'whk_123'));
    }

    public function testRejectsInvalidSignatureAndMissingPayloadShape(): void
    {
        $event = $this->signedEvent('whk_123');
        $event['hash'] = str_repeat('0', 64);

        self::assertFalse(Webhook::verifyEvent($event, 'whk_123'));

        $this->expectException(\InvalidArgumentException::class);
        Webhook::verifyEventOrThrow(['event' => ['type' => 'document_completed']], 'whk_123');
    }

    public function testFreshnessWindowIsOptionalButEnforcedWhenProvided(): void
    {
        $event = $this->signedEvent('whk_123', time: '1710000000');

        self::assertTrue(Webhook::verifyEvent($event, 'whk_123'));
        self::assertTrue(Webhook::verifyEvent($event, 'whk_123', 60, static fn (): int => 1710000030));
        self::assertFalse(Webhook::verifyEvent($event, 'whk_123', 60, static fn (): int => 1710000061));
    }

    public function testReplayProtectionRejectsDuplicateEvents(): void
    {
        $event = $this->signedEvent('whk_123', time: '1710000000');
        $store = new MemoryReplayStore(now: static fn (): int => 1710000000);

        self::assertTrue(Webhook::verifyEventOnce($event, 'whk_123', $store, 300, static fn (): int => 1710000001));
        self::assertFalse(Webhook::verifyEventOnce($event, 'whk_123', $store, 300, static fn (): int => 1710000002));
        self::assertSame('signwell:document_completed:1710000000:' . $event['hash'], Webhook::replayKey($event));

        try {
            Webhook::verifyEventOnceOrThrow($event, 'whk_123', $store, 300, static fn (): int => 1710000003);
            self::fail('Expected duplicate event to be rejected.');
        } catch (\InvalidArgumentException $error) {
            self::assertSame('webhook event has already been processed', $error->getMessage());
        }
    }

    public function testReplayProtectionEvictsOldestEntryAtCapacity(): void
    {
        $store = new MemoryReplayStore(maxEntries: 2, now: static fn (): int => 1710000000);
        $first = $this->signedEvent('whk_123', type: 'document_completed');
        $second = $this->signedEvent('whk_123', type: 'document_declined');
        $third = $this->signedEvent('whk_123', type: 'document_viewed');

        self::assertTrue(Webhook::verifyEventOnce(
            $first,
            'whk_123',
            $store,
            300,
            static fn (): int => 1710000001
        ));
        self::assertTrue(Webhook::verifyEventOnce(
            $second,
            'whk_123',
            $store,
            300,
            static fn (): int => 1710000002
        ));
        self::assertFalse(Webhook::verifyEventOnce(
            $second,
            'whk_123',
            $store,
            300,
            static fn (): int => 1710000003
        ));
        self::assertTrue(Webhook::verifyEventOnce(
            $third,
            'whk_123',
            $store,
            300,
            static fn (): int => 1710000004
        ));
        self::assertTrue(Webhook::verifyEventOnce(
            $first,
            'whk_123',
            $store,
            300,
            static fn (): int => 1710000005
        ));
    }

    /** @return array{type:string,time:string,hash:string} */
    private function signedEvent(string $webhookId, string $type = 'document_completed', string $time = '1710000000'): array
    {
        return [
            'type' => $type,
            'time' => $time,
            'hash' => hash_hmac('sha256', "{$type}@{$time}", $webhookId),
        ];
    }
}
