<?php

declare(strict_types=1);

// Source: signwell-sdk-generator/extras/php/overlay/lib/Embedded.php
// Do not edit the generated SDK copy directly.

namespace SignWell\Sdk;

use SignWell\Sdk\Models\DocumentFromTemplateRequest;
use SignWell\Sdk\Models\DocumentRequest;
use SignWell\Sdk\Models\FieldsInnerInner;
use SignWell\Sdk\Models\FilesInner;
use SignWell\Sdk\Models\RecipientsInner;
use SignWell\Sdk\Models\TemplateRecipientsInner;
use SignWell\Sdk\Resources\DocumentApi;

final class Embedded
{
    public const SCRIPT_URL = 'https://static.signwell.com/assets/embedded.js';

    private const SAFE_HANDLER_PATH = '/^[$A-Z_][0-9A-Z_$]*(?:\\.[$A-Z_][0-9A-Z_$]*)*$/i';
    private const BLOCKED_HANDLER_SEGMENTS = ['__proto__', 'constructor', 'prototype'];

    /** @param array<string, mixed> $params */
    public static function createSigningDocument(array $params, ?DocumentApi $documentApi = null): mixed
    {
        $recipients = self::buildRecipients(self::requiredArray($params, 'recipients'));
        $attrs = array_merge($params, [
            'test_mode' => (bool) ($params['test_mode'] ?? false),
            'files' => self::buildFiles(self::requiredArray($params, 'files')),
            'recipients' => $recipients,
            'embedded_signing' => true,
            'embedded_signing_notifications' => (bool) ($params['send_notifications'] ?? false),
        ]);
        unset($attrs['send_notifications']);

        if (array_key_exists('fields', $params)) {
            $attrs['fields'] = self::buildFields(self::requiredArray($params, 'fields'), $recipients);
        }

        $request = new DocumentRequest($attrs);
        self::validateSigningPlacement($request, $recipients);

        return ($documentApi ?? new DocumentApi())->createDocument($request);
    }

    /** @param array<string, mixed> $params */
    public static function createRequestingDocument(array $params, ?DocumentApi $documentApi = null): mixed
    {
        $attrs = array_merge($params, [
            'test_mode' => (bool) ($params['test_mode'] ?? false),
            'files' => self::buildFiles(self::requiredArray($params, 'files')),
            'recipients' => self::buildRecipients(self::requiredArray($params, 'recipients')),
            'draft' => true,
        ]);

        return ($documentApi ?? new DocumentApi())->createDocument(new DocumentRequest($attrs));
    }

    /** @param array<string, mixed> $params */
    public static function createSigningDocumentFromTemplate(array $params, ?DocumentApi $documentApi = null): mixed
    {
        $hasTemplateId = self::present($params['template_id'] ?? null);
        $hasTemplateIds = self::present($params['template_ids'] ?? null);
        if ($hasTemplateId && $hasTemplateIds) {
            throw new \InvalidArgumentException('Provide either template_id or template_ids, not both');
        }
        if (!$hasTemplateId && !$hasTemplateIds) {
            throw new \InvalidArgumentException('Provide template_id or template_ids');
        }

        $attrs = array_merge($params, [
            'test_mode' => (bool) ($params['test_mode'] ?? false),
            'recipients' => self::buildTemplateRecipients(self::requiredArray($params, 'recipients')),
            'embedded_signing' => true,
            'embedded_signing_notifications' => (bool) ($params['send_notifications'] ?? false),
        ]);
        unset($attrs['send_notifications']);

        return ($documentApi ?? new DocumentApi())->createDocumentFromTemplate(new DocumentFromTemplateRequest($attrs));
    }

    /** @return array<string, string> */
    public static function embeddedSigningUrls(mixed $document): array
    {
        $recipients = self::objectValue($document, 'getRecipients', 'recipients') ?? [];
        $urls = [];
        foreach ($recipients as $recipient) {
            $email = self::objectValue($recipient, 'getEmail', 'email');
            $url = self::objectValue($recipient, 'getEmbeddedSigningUrl', 'embedded_signing_url');
            if (self::present($email) && self::present($url)) {
                $urls[(string) $email] = (string) $url;
            }
        }

        return $urls;
    }

    public static function embeddedSigningUrl(mixed $document, int $recipientIndex = 0): ?string
    {
        $recipients = self::objectValue($document, 'getRecipients', 'recipients') ?? [];
        $recipient = $recipients[$recipientIndex] ?? null;
        if ($recipient === null) {
            return null;
        }

        $url = self::objectValue($recipient, 'getEmbeddedSigningUrl', 'embedded_signing_url');

        return $url === null ? null : (string) $url;
    }

    public static function scriptTag(?string $nonce = null): string
    {
        return '<script' . self::nonceAttribute(self::optionalString($nonce)) . ' src="' . self::SCRIPT_URL . '"></script>';
    }

    /** @param array<string, mixed> $options */
    public static function signingIframe(array $options): string
    {
        $jsOptions = self::buildIframeOptions(
            $options,
            ['containerId' => 'container_id'],
            [
                'allowDecline' => 'allow_decline',
                'allowClose' => 'allow_close',
                'showHeader' => 'show_header',
                'allowDownload' => 'allow_download',
            ],
            ['redirectUrl' => 'redirect_url', 'declineRedirectUrl' => 'decline_redirect_url']
        );

        return self::buildEmbedScript(
            $jsOptions,
            $options['events'] ?? [],
            (bool) ($options['auto_open'] ?? true),
            self::optionalString($options['nonce'] ?? null)
        );
    }

    /** @param array<string, mixed> $options */
    public static function requestingIframe(array $options): string
    {
        $jsOptions = self::buildIframeOptions(
            $options,
            ['containerId' => 'container_id'],
            [
                'allowClose' => 'allow_close',
                'showHeader' => 'show_header',
                'allowDownload' => 'allow_download',
                'showSendButton' => 'show_send_button',
            ],
            ['redirectUrl' => 'redirect_url']
        );

        return self::buildEmbedScript(
            $jsOptions,
            $options['events'] ?? [],
            (bool) ($options['auto_open'] ?? true),
            self::optionalString($options['nonce'] ?? null)
        );
    }

    /** @param array<int, array<string, mixed>> $recipients */
    private static function buildRecipients(array $recipients): array
    {
        return array_map(
            static fn (array $recipient, int $index): RecipientsInner => new RecipientsInner(self::recipientAttrs($recipient, $index)),
            $recipients,
            array_keys($recipients)
        );
    }

    /** @param array<int, array<string, mixed>> $recipients */
    private static function buildTemplateRecipients(array $recipients): array
    {
        return array_map(
            static function (array $recipient, int $index): TemplateRecipientsInner {
                $attrs = self::recipientAttrs($recipient, $index);
                if (self::present($recipient['placeholder_name'] ?? null)) {
                    $attrs['placeholder_name'] = $recipient['placeholder_name'];
                }

                return new TemplateRecipientsInner($attrs);
            },
            $recipients,
            array_keys($recipients)
        );
    }

    /** @param array<int, array<string, mixed>> $files */
    private static function buildFiles(array $files): array
    {
        return array_map(static function (array $file): FilesInner {
            $hasUrl = self::present($file['file_url'] ?? null);
            $hasBase64 = self::present($file['file_base64'] ?? null);
            if (!self::present($file['name'] ?? null)) {
                throw new \InvalidArgumentException('Each file must include name');
            }
            if ($hasUrl === $hasBase64) {
                throw new \InvalidArgumentException('Each file must include exactly one of file_url or file_base64');
            }

            $attrs = [
                'name' => $file['name'],
            ];
            if ($hasUrl) {
                $attrs['file_url'] = $file['file_url'];
            } else {
                $attrs['file_base64'] = self::normalizeBase64File((string) $file['name'], $file['file_base64']);
            }

            return new FilesInner($attrs);
        }, $files);
    }

    private static function normalizeBase64File(string $name, mixed $fileBase64): string
    {
        if (!is_scalar($fileBase64)) {
            throw new \InvalidArgumentException('file_base64 must be a string');
        }

        $normalized = preg_replace('/\s+/', '', (string) $fileBase64);
        if ($normalized === null || $normalized === '') {
            throw new \InvalidArgumentException('file_base64 must be a non-empty RFC 4648 base64 string');
        }

        $decoded = base64_decode($normalized, true);
        if ($decoded === false || $decoded === '') {
            throw new \InvalidArgumentException('file_base64 must be a valid RFC 4648 base64 string');
        }

        if (strtolower(pathinfo($name, PATHINFO_EXTENSION)) === 'pdf') {
            $tail = substr($decoded, -2048);
            if (!str_starts_with($decoded, '%PDF-') || !str_contains($tail, '%%EOF')) {
                throw new \InvalidArgumentException('PDF file_base64 must decode to a complete PDF document');
            }
        }

        return $normalized;
    }

    /** @param array<int, array<int, array<string, mixed>>> $fields */
    private static function buildFields(array $fields, array $recipients): array
    {
        $defaultRecipientId = $recipients[0]?->getId();

        return array_map(static function (array $fileFields) use ($defaultRecipientId): array {
            return array_map(static function (array $field) use ($defaultRecipientId): FieldsInnerInner {
                $field['recipient_id'] = self::present($field['recipient_id'] ?? null) ? $field['recipient_id'] : $defaultRecipientId;
                $field['required'] = $field['required'] ?? true;
                if (!self::present($field['recipient_id'] ?? null)) {
                    throw new \InvalidArgumentException('Each field must include recipient_id when no default recipient exists');
                }

                return new FieldsInnerInner($field);
            }, $fileFields);
        }, $fields);
    }

    /** @param array<int, RecipientsInner> $recipients */
    private static function validateSigningPlacement(DocumentRequest $request, array $recipients): void
    {
        if ($request->getWithSignaturePage() === true || $request->getTextTags() === true) {
            return;
        }

        $fields = [];
        foreach (($request->getFields() ?? []) as $fileFields) {
            foreach ($fileFields as $field) {
                $fields[] = $field;
            }
        }

        $assignedRecipientIds = [];
        foreach ($fields as $field) {
            $recipientId = $field->getRecipientId();
            if (self::present($recipientId)) {
                $assignedRecipientIds[(string) $recipientId] = true;
            }
        }

        $missingRecipient = false;
        foreach ($recipients as $recipient) {
            $recipientId = $recipient->getId();
            if (self::present($recipientId) && !isset($assignedRecipientIds[(string) $recipientId])) {
                $missingRecipient = true;
                break;
            }
        }

        if ($fields === [] || $missingRecipient) {
            throw new \InvalidArgumentException(
                'Embedded signing documents must include fields for every recipient, set with_signature_page: true, or set text_tags: true'
            );
        }
    }

    /** @param array<string, mixed> $recipient */
    private static function recipientAttrs(array $recipient, int $index): array
    {
        $attrs = [
            'id' => self::present($recipient['id'] ?? null) ? $recipient['id'] : (string) ($index + 1),
            'name' => $recipient['name'] ?? null,
            'email' => $recipient['email'] ?? null,
        ];
        $passcode = self::optionalString($recipient['passcode'] ?? null);
        if ($passcode !== null) {
            $attrs['passcode'] = $passcode;
        }

        return $attrs;
    }

    /** @param array<string, mixed> $options */
    private static function buildIframeOptions(array $options, array $values, array $booleans, array $redirects): array
    {
        if (!isset($options['url'])) {
            throw new \InvalidArgumentException('url is required');
        }

        $jsOptions = [
            'url' => self::validateEmbedUrl((string) $options['url'], $options['allowed_embed_hosts'] ?? null),
        ];
        foreach ($values as $jsKey => $optionKey) {
            if (array_key_exists($optionKey, $options) && $options[$optionKey] !== null) {
                $jsOptions[$jsKey] = (string) $options[$optionKey];
            }
        }
        foreach ($booleans as $jsKey => $optionKey) {
            if (array_key_exists($optionKey, $options) && $options[$optionKey] !== null) {
                $jsOptions[$jsKey] = (bool) $options[$optionKey];
            }
        }
        foreach ($redirects as $jsKey => $optionKey) {
            if (array_key_exists($optionKey, $options) && $options[$optionKey] !== null) {
                $jsOptions[$jsKey] = self::validateRedirectUrl((string) $options[$optionKey], $options['allowed_redirect_hosts'] ?? null);
            }
        }

        return $jsOptions;
    }

    /** @param array<string, mixed> $events */
    private static function buildEmbedScript(array $jsOptions, array $events, bool $autoOpen, ?string $nonce): string
    {
        $optionsJson = self::safeJson($jsOptions);
        $eventsJson = self::safeJson(self::normalizeEventPaths($events));
        $openLine = $autoOpen ? "\n  signwellEmbed.open();" : '';
        $nonceAttribute = self::nonceAttribute($nonce);

        return <<<HTML
<script{$nonceAttribute}>(function() {
  var config = {$optionsJson};
  var eventPaths = {$eventsJson};
  var resolveSignWellHandler = function(path) {
    return path.split('.').reduce(function(context, key) {
      return context && context[key];
    }, window);
  };
  config.events = config.events || {};
  Object.keys(eventPaths).forEach(function(name) {
    var handler = resolveSignWellHandler(eventPaths[name]);
    if (typeof handler === 'function') {
      config.events[name] = handler;
    }
  });
  var signwellEmbed = new SignWellEmbed(config);{$openLine}
})();</script>
HTML;
    }

    /** @param array<string, mixed> $events */
    private static function normalizeEventPaths(array $events): array
    {
        $normalized = [];
        foreach ($events as $eventName => $handlerPath) {
            if ($handlerPath === null) {
                continue;
            }
            $path = trim((string) $handlerPath);
            if (!preg_match(self::SAFE_HANDLER_PATH, $path)) {
                throw new \InvalidArgumentException("Event handler paths must be dot-separated JavaScript identifiers. Invalid handler for {$eventName}");
            }
            foreach (explode('.', $path) as $segment) {
                if (in_array($segment, self::BLOCKED_HANDLER_SEGMENTS, true)) {
                    throw new \InvalidArgumentException("Event handler paths cannot include prototype-chain segments. Invalid handler for {$eventName}");
                }
            }
            $normalized[(string) $eventName] = $path;
        }

        return $normalized;
    }

    /** @param string[]|null $allowedHosts */
    private static function validateEmbedUrl(string $url, ?array $allowedHosts): string
    {
        $parts = self::parseHttpsUrl($url, 'Embed URL');
        $host = strtolower($parts['host']);
        if (!self::isDefaultSignWellHost($host) && !in_array($host, self::normalizeAllowedHosts($allowedHosts), true)) {
            throw new \InvalidArgumentException('Embed URL host is not allowed');
        }

        return $url;
    }

    /** @param string[]|null $allowedHosts */
    private static function validateRedirectUrl(string $url, ?array $allowedHosts): string
    {
        $parts = self::parseHttpsUrl($url, 'Redirect URL');
        $allowed = self::normalizeAllowedHosts($allowedHosts);
        if ($allowed !== [] && !in_array(strtolower($parts['host']), $allowed, true)) {
            throw new \InvalidArgumentException('Redirect URL host is not allowed');
        }

        return $url;
    }

    /** @return array{scheme:string,host:string,user?:string,pass?:string} */
    private static function parseHttpsUrl(string $url, string $label): array
    {
        $parts = parse_url($url);
        if (!is_array($parts)) {
            throw new \InvalidArgumentException("{$label} is invalid");
        }
        if (($parts['scheme'] ?? null) !== 'https') {
            throw new \InvalidArgumentException("{$label} must use HTTPS");
        }
        if (!self::present($parts['host'] ?? null)) {
            throw new \InvalidArgumentException("{$label} host is required");
        }
        if (isset($parts['user']) || isset($parts['pass'])) {
            throw new \InvalidArgumentException("{$label} must not include credentials");
        }

        return $parts;
    }

    private static function isDefaultSignWellHost(string $host): bool
    {
        return $host === 'signwell.com' || str_ends_with($host, '.signwell.com');
    }

    /** @param string[]|null $allowedHosts */
    private static function normalizeAllowedHosts(?array $allowedHosts): array
    {
        $normalized = [];
        foreach ($allowedHosts ?? [] as $host) {
            $value = strtolower(trim((string) $host));
            if ($value === '' || str_contains($value, '/') || str_contains($value, '@') || str_contains($value, ':')) {
                throw new \InvalidArgumentException('Allowed hosts must be exact hostnames');
            }
            $normalized[] = $value;
        }

        return $normalized;
    }

    /** @param array<string, mixed> $params */
    private static function requiredArray(array $params, string $key): array
    {
        $value = $params[$key] ?? null;
        if (!is_array($value)) {
            throw new \InvalidArgumentException("{$key} must be an array");
        }

        return $value;
    }

    private static function objectValue(mixed $object, string $getter, string $arrayKey): mixed
    {
        if (is_object($object) && method_exists($object, $getter)) {
            return $object->{$getter}();
        }
        if (is_array($object)) {
            return $object[$arrayKey] ?? null;
        }

        return null;
    }

    private static function optionalString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $normalized = trim((string) $value);

        return $normalized === '' ? null : $normalized;
    }

    private static function present(mixed $value): bool
    {
        return $value !== null && $value !== '' && $value !== [];
    }

    private static function nonceAttribute(?string $nonce): string
    {
        return $nonce === null ? '' : ' nonce="' . self::escapeHtmlAttribute($nonce) . '"';
    }

    private static function escapeHtmlAttribute(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    private static function safeJson(mixed $value): string
    {
        return json_encode($value, JSON_THROW_ON_ERROR | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    }
}
