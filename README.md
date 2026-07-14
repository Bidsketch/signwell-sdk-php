# SignWell PHP SDK

PHP SDK for the SignWell API.

## Install

```bash
composer require signwell/signwell-sdk-php
```

Requires PHP 8.2 or newer.

## Quick Start

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use SignWell\Sdk\Client;

$signwell = Client::fromApiKey(getenv('SIGNWELL_API_KEY') ?: '');

$document = $signwell->createDocument([
    'test_mode' => true,
    'name' => 'NDA',
    'files' => [
        ['name' => 'nda.pdf', 'file_url' => 'https://example.com/nda.pdf'],
    ],
    'recipients' => [
        ['id' => '1', 'name' => 'Jane Doe', 'email' => 'jane@example.com'],
    ],
    'fields' => [
        [
            ['page' => 1, 'x' => 120, 'y' => 240, 'type' => 'signature', 'recipient_id' => '1'],
        ],
    ],
]);
```

## Resources, Models, And Errors

```php
use SignWell\Sdk\Errors;
use SignWell\Sdk\Models;
use SignWell\Sdk\Resources;

$documents = new Resources\DocumentApi();
$request = new Models\DocumentRequest(['name' => 'NDA']);

try {
    $documents->getDocument('document-id');
} catch (Errors\NotFoundError $error) {
    // Document was not found.
} catch (Errors\RateLimitError $error) {
    // Retry after backing off.
} catch (Errors\ApiError $error) {
    // Generic SDK error.
}
```

## Binary And JSON Response Modes

Some SignWell endpoints can return either binary files or JSON objects. The SDK selects the expected response type from the request flags.

```php
$documents = $signwell->documents();

$pdf = $documents->getCompletedPdf('document-id');
// $pdf is an SplFileObject.

$urlResponse = $documents->getCompletedPdf('document-id', url_only: true);
// $urlResponse is a SignWell\Sdk\Models\CompletedPdfUrlResponse.

$regional = $signwell->regional();
$certificate = $regional->getNom151Certificate('document-id', object_only: true);
```

`getNom151Certificate` rejects `url_only: true` and `object_only: true` together.

## Convenience Workflows

`Client` provides thin wrappers for common API workflows while keeping the generated resources available when you need every option.

```php
$account = $signwell->getMe();
$document = $signwell->createDocument([...]);
$templateDocument = $signwell->createDocumentFromTemplate([...]);
$sent = $signwell->sendDocument('draft-document-id', ['subject' => 'Please sign']);
$signwell->sendReminder('document-id');
$template = $signwell->createTemplate([...]);
$updatedTemplate = $signwell->updateTemplate('template-id', ['name' => 'Updated NDA']);
$bulkSend = $signwell->createBulkSend([...]);
$csvCheck = $signwell->validateBulkSendCsv([...]);
$csvTemplate = $signwell->getBulkSendCsvTemplate(['template-id'], base64: true);
```

## Embedded Helpers

```php
use SignWell\Sdk\Client;

$signwell = Client::fromApiKey(getenv('SIGNWELL_API_KEY') ?: '');

$document = $signwell->createSigningDocument([
    'name' => 'NDA',
    'test_mode' => true,
    'files' => [
        ['name' => 'nda.pdf', 'file_url' => 'https://example.com/nda.pdf'],
    ],
    'recipients' => [
        ['name' => 'Jane Doe', 'email' => 'jane@example.com'],
    ],
    'fields' => [
        [
            ['page' => 1, 'x' => 120, 'y' => 240, 'type' => 'signature'],
        ],
    ],
]);

$signingUrl = $signwell->embeddedSigningUrl($document);
$nonce = base64_encode(random_bytes(16));
$loader = $signwell->scriptTag($nonce);
$script = $signwell->signingIframe([
    'url' => $signingUrl,
    'nonce' => $nonce,
    'events' => ['completed' => 'SignWellHandlers.completed'],
]);
```

Embedded signing documents must include fields for every recipient, set `with_signature_page: true`, or set `text_tags: true`. The helper validates this before calling the API.

Pass the same CSP nonce to `scriptTag()` and iframe helpers when your page uses nonce-based `script-src`.

The static `SignWell\Sdk\Embedded` helper remains available when you do not need a configured client object.

## Webhook Verification

```php
use SignWell\Sdk\Webhook;
use SignWell\Sdk\Webhook\MemoryReplayStore;

$payload = json_decode(file_get_contents('php://input'), true, flags: JSON_THROW_ON_ERROR);
$event = $payload['event'];
$webhookId = getenv('SIGNWELL_WEBHOOK_ID');
if (!is_string($webhookId) || $webhookId === '') {
    throw new RuntimeException('SIGNWELL_WEBHOOK_ID must be set.');
}

Webhook::verifyEventOrThrow(
    event: $event,
    webhookId: $webhookId,
    toleranceSeconds: 300,
);

$store = new MemoryReplayStore();
Webhook::verifyEventOnceOrThrow(
    event: $event,
    webhookId: $webhookId,
    replayStore: $store,
    toleranceSeconds: 300,
);
```

Use `verifyEventOnce` or `verifyEventOnceOrThrow` with an application-provided atomic replay store for side-effecting webhook handlers. Replay stores should return `false` only for duplicate keys. The included memory store is for local development and single-process examples; when full, it evicts the oldest unexpired entry.

## Laravel

The package auto-discovers `SignWell\Sdk\Laravel\SignWellServiceProvider` when installed in a Laravel app. Publish configuration:

```bash
php artisan vendor:publish --tag=signwell-config
```

Set `SIGNWELL_API_KEY` in your environment, then resolve APIs from the container:

```php
$documents = app(SignWell\Sdk\Resources\DocumentApi::class);
$document = $documents->getDocument('document-id');
```

The facade exposes configured resources and the same convenience helpers as the vanilla client:

```php
$documents = SignWell::documents();

$document = SignWell::createSigningDocument([
    'name' => 'NDA',
    'test_mode' => true,
    'files' => [['name' => 'nda.pdf', 'file_url' => 'https://example.com/nda.pdf']],
    'recipients' => [['name' => 'Jane Doe', 'email' => 'jane@example.com']],
    'fields' => [[['page' => 1, 'x' => 120, 'y' => 240, 'type' => 'signature']]],
]);
```

Use `SignWell\Sdk\Webhook` directly for webhook signature verification.

## Examples

The generated package includes runnable examples:

```bash
SIGNWELL_API_KEY=your_key php examples/vanilla_send_request.php
SIGNWELL_API_KEY=your_key php examples/laravel_facade_send_request.php
```

These examples create test-mode signing requests. Set `SIGNWELL_EXAMPLE_RECIPIENT_EMAIL` and `SIGNWELL_EXAMPLE_RECIPIENT_NAME` to override the placeholder signer.

## Documentation

Generated API and model references are in `docs/`.

## License

MIT
