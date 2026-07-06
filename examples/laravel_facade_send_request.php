<?php

declare(strict_types=1);

use SignWell\Sdk\Configuration;
use SignWell\Sdk\Laravel\Facades\SignWell;
use SignWell\Sdk\Laravel\SignWellManager;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/_send_request_payload.php';

$apiKey = getenv('SIGNWELL_API_KEY');
if (!is_string($apiKey) || $apiKey === '') {
    fwrite(STDERR, "SIGNWELL_API_KEY is required\n");
    exit(1);
}

$configuration = (new Configuration())->setApiKey('X-Api-Key', $apiKey);
SignWell::swap(new SignWellManager($configuration));

$document = SignWell::createDocument(signwellExampleDocumentPayload());

echo 'Created test-mode signing request ' . $document->getId() . " via Laravel facade\n";
