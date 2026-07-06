<?php

declare(strict_types=1);

// Source: signwell-sdk-generator/extras/php/overlay/lib/Webhook/ReplayStoreInterface.php
// Do not edit the generated SDK copy directly.

namespace SignWell\Sdk\Webhook;

interface ReplayStoreInterface
{
    /**
     * @return bool false only when the replay key already exists
     *
     * @throws ReplayStoreCapacityExceededException when the store cannot accept new keys
     */
    public function add(string $key, float $expiresAtUnixSeconds): bool;
}
