<?php

declare(strict_types=1);

// Source: signwell-sdk-generator/extras/php/overlay/lib/Laravel/SignWellManager.php
// Do not edit the generated SDK copy directly.

namespace SignWell\Sdk\Laravel;

use SignWell\Sdk\Client;
use SignWell\Sdk\Configuration;
use SignWell\Sdk\Embedded;

final class SignWellManager extends Client
{
    public function __construct(Configuration $configuration)
    {
        parent::__construct(configuration: $configuration);
    }

    public function embedded(): string
    {
        return Embedded::class;
    }
}
