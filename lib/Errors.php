<?php

declare(strict_types=1);

// Source: signwell-sdk-generator/extras/php/overlay/lib/Errors.php
// Do not edit the generated SDK copy directly.

namespace SignWell\Sdk\Errors;

final class RateLimitInfo
{
    public function __construct(
        public readonly ?float $limit = null,
        public readonly ?float $remaining = null,
        public readonly ?float $reset = null,
        public readonly ?\DateTimeImmutable $resetAt = null,
        public readonly ?float $retryAfter = null,
        public readonly ?\DateTimeImmutable $retryAfterAt = null
    ) {
    }

    /**
     * @param string[][]|null $headers
     */
    public static function fromHeaders(?array $headers): ?self
    {
        if ($headers === null) {
            return null;
        }

        $limit = self::parseNumber(self::firstHeader($headers, ['x-ratelimit-limit', 'ratelimit-limit']));
        $remaining = self::parseNumber(self::firstHeader($headers, ['x-ratelimit-remaining', 'ratelimit-remaining']));
        $reset = self::parseNumber(self::firstHeader($headers, ['x-ratelimit-reset', 'ratelimit-reset']));
        $retryAfterValue = self::firstHeader($headers, ['retry-after']);
        $retryAfter = self::parseNumber($retryAfterValue);
        $retryAfterAt = $retryAfter === null ? self::parseDate($retryAfterValue) : null;
        $resetAt = $reset !== null && $reset > 1_000_000_000 ? (new \DateTimeImmutable())->setTimestamp((int) $reset) : null;

        if ($limit === null && $remaining === null && $reset === null && $retryAfter === null && $retryAfterAt === null) {
            return null;
        }

        return new self($limit, $remaining, $reset, $resetAt, $retryAfter, $retryAfterAt);
    }

    /**
     * @param string[][] $headers
     * @param list<string> $names
     */
    private static function firstHeader(array $headers, array $names): ?string
    {
        foreach ($headers as $headerName => $values) {
            foreach ($names as $name) {
                if (strcasecmp((string) $headerName, $name) !== 0) {
                    continue;
                }
                foreach ((array) $values as $value) {
                    if (is_string($value) && trim($value) !== '') {
                        return $value;
                    }
                }
            }
        }

        return null;
    }

    private static function parseNumber(?string $value): ?float
    {
        if ($value === null || !preg_match('/^-?\d+(?:\.\d+)?/', trim($value), $matches)) {
            return null;
        }

        return (float) $matches[0];
    }

    private static function parseDate(?string $value): ?\DateTimeImmutable
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        try {
            return new \DateTimeImmutable($value);
        } catch (\Exception) {
            return null;
        }
    }
}

class ApiError extends \Exception
{
}

class ApiStatusError extends \SignWell\Sdk\ApiException
{
}

class BadRequestError extends ApiStatusError
{
}

class AuthenticationError extends ApiStatusError
{
}

class ForbiddenError extends ApiStatusError
{
}

class PermissionDeniedError extends ForbiddenError
{
}

class NotFoundError extends ApiStatusError
{
}

class ConflictError extends ApiStatusError
{
}

class UnprocessableEntityError extends ApiStatusError
{
}

class RateLimitError extends ApiStatusError
{
}

class InternalServerError extends ApiStatusError
{
}

class ApiConnectionError extends \SignWell\Sdk\ApiException
{
}

class TransportError extends ApiConnectionError
{
}

class ApiTimeoutError extends ApiConnectionError
{
}

class UnsupportedContentTypeError extends \SignWell\Sdk\ApiException
{
}

class WaitForCompletionTimeoutError extends ApiError
{
    public function __construct(string $message, public readonly mixed $lastDocument = null)
    {
        parent::__construct($message);
    }
}

namespace SignWell\Sdk;

\class_alias(Errors\ApiError::class, __NAMESPACE__ . '\\ApiError');
\class_alias(Errors\ApiStatusError::class, __NAMESPACE__ . '\\ApiStatusError');
\class_alias(Errors\BadRequestError::class, __NAMESPACE__ . '\\BadRequestError');
\class_alias(Errors\AuthenticationError::class, __NAMESPACE__ . '\\AuthenticationError');
\class_alias(Errors\ForbiddenError::class, __NAMESPACE__ . '\\ForbiddenError');
\class_alias(Errors\PermissionDeniedError::class, __NAMESPACE__ . '\\PermissionDeniedError');
\class_alias(Errors\NotFoundError::class, __NAMESPACE__ . '\\NotFoundError');
\class_alias(Errors\ConflictError::class, __NAMESPACE__ . '\\ConflictError');
\class_alias(Errors\UnprocessableEntityError::class, __NAMESPACE__ . '\\UnprocessableEntityError');
\class_alias(Errors\RateLimitError::class, __NAMESPACE__ . '\\RateLimitError');
\class_alias(Errors\InternalServerError::class, __NAMESPACE__ . '\\InternalServerError');
\class_alias(Errors\ApiConnectionError::class, __NAMESPACE__ . '\\ApiConnectionError');
\class_alias(Errors\TransportError::class, __NAMESPACE__ . '\\TransportError');
\class_alias(Errors\ApiTimeoutError::class, __NAMESPACE__ . '\\ApiTimeoutError');
\class_alias(Errors\UnsupportedContentTypeError::class, __NAMESPACE__ . '\\UnsupportedContentTypeError');
\class_alias(Errors\WaitForCompletionTimeoutError::class, __NAMESPACE__ . '\\WaitForCompletionTimeoutError');
