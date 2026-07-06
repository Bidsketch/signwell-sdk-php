<?php

declare(strict_types=1);

use SignWell\Sdk\Client;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/_send_request_payload.php';

$apiKey = getenv('SIGNWELL_API_KEY');
if (!is_string($apiKey) || $apiKey === '') {
    fwrite(STDERR, "SIGNWELL_API_KEY is required\n");
    exit(1);
}

$document = Client::fromApiKey($apiKey)->createDocument(signwellExampleDocumentPayload());

echo 'Created test-mode signing request ' . $document->getId() . "\n";
