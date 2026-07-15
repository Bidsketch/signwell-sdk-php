<?php

declare(strict_types=1);

// Source: signwell-sdk-generator/extras/php/overlay/lib/Client.php
// Do not edit the generated SDK copy directly.

namespace SignWell\Sdk;

use GuzzleHttp\ClientInterface;
use SignWell\Sdk\Models\BulkSendCsvRequest;
use SignWell\Sdk\Models\CreateBulkSendRequest;
use SignWell\Sdk\Models\DocumentFromTemplateRequest;
use SignWell\Sdk\Models\DocumentRequest;
use SignWell\Sdk\Models\DocumentTemplateRequest;
use SignWell\Sdk\Models\DocumentTemplateUpdateRequest;
use SignWell\Sdk\Models\SendReminderRequest;
use SignWell\Sdk\Models\UpdateDocumentAndSendRequest;
use SignWell\Sdk\Resources\ApiApplicationApi;
use SignWell\Sdk\Resources\BulkSendApi;
use SignWell\Sdk\Resources\DocumentApi;
use SignWell\Sdk\Resources\MeApi;
use SignWell\Sdk\Resources\RegionalApi;
use SignWell\Sdk\Resources\TemplateApi;
use SignWell\Sdk\Resources\WebhooksApi;

class Client
{
    public function __construct(
        private readonly Configuration $configuration,
        private readonly ?ClientInterface $httpClient = null,
        private readonly ?HeaderSelector $headerSelector = null,
        private readonly int $hostIndex = 0
    ) {
    }

    public static function fromApiKey(
        string $apiKey,
        ?string $host = null,
        ?ClientInterface $httpClient = null,
        ?HeaderSelector $headerSelector = null,
        int $hostIndex = 0
    ): self {
        $configuration = new Configuration();
        $configuration->setApiKey('X-Api-Key', $apiKey);
        if ($host !== null && $host !== '') {
            $configuration->setHost($host);
        }

        return new self($configuration, $httpClient, $headerSelector, $hostIndex);
    }

    public function configuration(): Configuration
    {
        return $this->configuration;
    }

    public function documents(): DocumentApi
    {
        return new DocumentApi($this->httpClient, $this->configuration, $this->headerSelector, $this->hostIndex);
    }

    public function templates(): TemplateApi
    {
        return new TemplateApi($this->httpClient, $this->configuration, $this->headerSelector, $this->hostIndex);
    }

    public function bulkSends(): BulkSendApi
    {
        return new BulkSendApi($this->httpClient, $this->configuration, $this->headerSelector, $this->hostIndex);
    }

    public function regional(): RegionalApi
    {
        return new RegionalApi($this->httpClient, $this->configuration, $this->headerSelector, $this->hostIndex);
    }

    public function me(): MeApi
    {
        return new MeApi($this->httpClient, $this->configuration, $this->headerSelector, $this->hostIndex);
    }

    public function apiApplications(): ApiApplicationApi
    {
        return new ApiApplicationApi($this->httpClient, $this->configuration, $this->headerSelector, $this->hostIndex);
    }

    public function webhooks(): WebhooksApi
    {
        return new WebhooksApi($this->httpClient, $this->configuration, $this->headerSelector, $this->hostIndex);
    }

    /** @param array<string, mixed> $params */
    public function createDocument(array $params): mixed
    {
        return $this->documents()->createDocument(new DocumentRequest($params));
    }

    /** @param array<string, mixed> $params */
    public function createDocumentFromTemplate(array $params): mixed
    {
        return $this->documents()->createDocumentFromTemplate(new DocumentFromTemplateRequest($params));
    }

    /** @param array<string, mixed> $params */
    public function sendDocument(string $id, array $params = []): mixed
    {
        return $this->documents()->sendDocument($id, new UpdateDocumentAndSendRequest($params));
    }

    /** @param array<string, mixed> $params */
    public function sendReminder(string $id, array $params = []): void
    {
        $this->documents()->sendReminder($id, new SendReminderRequest($params));
    }

    public function getCompletedPdf(string $id, bool $urlOnly = false, bool $auditPage = true, ?string $fileFormat = null): mixed
    {
        return $this->documents()->getCompletedPdf($id, $urlOnly, $auditPage, $fileFormat);
    }

    /** @param array<string, mixed> $params */
    public function createSigningDocument(array $params): mixed
    {
        return Embedded::createSigningDocument($params, $this->documents());
    }

    /** @param array<string, mixed> $params */
    public function createRequestingDocument(array $params): mixed
    {
        return Embedded::createRequestingDocument($params, $this->documents());
    }

    /** @param array<string, mixed> $params */
    public function createSigningDocumentFromTemplate(array $params): mixed
    {
        return Embedded::createSigningDocumentFromTemplate($params, $this->documents());
    }

    /** @return array<string, string> */
    public function embeddedSigningUrls(mixed $document): array
    {
        return Embedded::embeddedSigningUrls($document);
    }

    public function embeddedSigningUrl(mixed $document, int $recipientIndex = 0): ?string
    {
        return Embedded::embeddedSigningUrl($document, $recipientIndex);
    }

    public function scriptTag(?string $nonce = null): string
    {
        return Embedded::scriptTag($nonce);
    }

    /** @param array<string, mixed> $options */
    public function signingIframe(array $options): string
    {
        return Embedded::signingIframe($options);
    }

    /** @param array<string, mixed> $options */
    public function requestingIframe(array $options): string
    {
        return Embedded::requestingIframe($options);
    }

    /** @param array<string, mixed> $params */
    public function createTemplate(array $params): mixed
    {
        return $this->templates()->createTemplate(new DocumentTemplateRequest($params));
    }

    /** @param array<string, mixed> $params */
    public function updateTemplate(string $id, array $params): mixed
    {
        return $this->templates()->updateTemplate($id, new DocumentTemplateUpdateRequest($params));
    }

    /** @param array<string, mixed> $params */
    public function createBulkSend(array $params): mixed
    {
        return $this->bulkSends()->createBulkSend(new CreateBulkSendRequest($params));
    }

    /** @param array<string, mixed> $params */
    public function validateBulkSendCsv(array $params): mixed
    {
        return $this->bulkSends()->validateBulkSendCsv(new BulkSendCsvRequest($params));
    }

    /** @param string[] $templateIds */
    public function getBulkSendCsvTemplate(array $templateIds, ?bool $base64 = null): mixed
    {
        return $this->bulkSends()->getBulkSendCsvTemplate($templateIds, $base64);
    }

    public function getMe(): mixed
    {
        return $this->me()->getMe();
    }
}
