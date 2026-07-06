<?php

declare(strict_types=1);

// Source: signwell-sdk-generator/extras/php/overlay/lib/Laravel/Facades/SignWell.php
// Do not edit the generated SDK copy directly.

namespace SignWell\Sdk\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \SignWell\Sdk\Configuration configuration()
 * @method static \SignWell\Sdk\Resources\DocumentApi documents()
 * @method static \SignWell\Sdk\Resources\TemplateApi templates()
 * @method static \SignWell\Sdk\Resources\BulkSendApi bulkSends()
 * @method static \SignWell\Sdk\Resources\RegionalApi regional()
 * @method static \SignWell\Sdk\Resources\MeApi me()
 * @method static \SignWell\Sdk\Resources\ApiApplicationApi apiApplications()
 * @method static mixed createDocument(array $params)
 * @method static mixed createDocumentFromTemplate(array $params)
 * @method static mixed sendDocument(string $id, array $params = [])
 * @method static void sendReminder(string $id, array $params = [])
 * @method static mixed getCompletedPdf(string $id, bool $urlOnly = false, bool $auditPage = true, string|null $fileFormat = null)
 * @method static mixed createSigningDocument(array $params)
 * @method static mixed createRequestingDocument(array $params)
 * @method static mixed createSigningDocumentFromTemplate(array $params)
 * @method static array<string, string> embeddedSigningUrls(mixed $document)
 * @method static string|null embeddedSigningUrl(mixed $document, int $recipientIndex = 0)
 * @method static string scriptTag(string|null $nonce = null)
 * @method static string signingIframe(array $options)
 * @method static string requestingIframe(array $options)
 * @method static mixed createTemplate(array $params)
 * @method static mixed updateTemplate(string $id, array $params)
 * @method static mixed createBulkSend(array $params)
 * @method static mixed validateBulkSendCsv(array $params)
 * @method static mixed getBulkSendCsvTemplate(array $templateIds, bool|null $base64 = null)
 * @method static mixed getMe()
 * @method static class-string<\SignWell\Sdk\Embedded> embedded()
 */
final class SignWell extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'signwell';
    }
}
