<?php

declare(strict_types=1);

// Source: signwell-sdk-generator/extras/php/overlay/lib/Errors.php
// Do not edit the generated SDK copy directly.

namespace SignWell\Sdk\Errors;

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

class ApiTimeoutError extends ApiConnectionError
{
}

class UnsupportedContentTypeError extends \SignWell\Sdk\ApiException
{
}

namespace SignWell\Sdk;

\class_alias(Errors\ApiError::class, __NAMESPACE__ . '\\ApiError');
\class_alias(Errors\ApiStatusError::class, __NAMESPACE__ . '\\ApiStatusError');
\class_alias(Errors\BadRequestError::class, __NAMESPACE__ . '\\BadRequestError');
\class_alias(Errors\AuthenticationError::class, __NAMESPACE__ . '\\AuthenticationError');
\class_alias(Errors\ForbiddenError::class, __NAMESPACE__ . '\\ForbiddenError');
\class_alias(Errors\NotFoundError::class, __NAMESPACE__ . '\\NotFoundError');
\class_alias(Errors\ConflictError::class, __NAMESPACE__ . '\\ConflictError');
\class_alias(Errors\UnprocessableEntityError::class, __NAMESPACE__ . '\\UnprocessableEntityError');
\class_alias(Errors\RateLimitError::class, __NAMESPACE__ . '\\RateLimitError');
\class_alias(Errors\InternalServerError::class, __NAMESPACE__ . '\\InternalServerError');
\class_alias(Errors\ApiConnectionError::class, __NAMESPACE__ . '\\ApiConnectionError');
\class_alias(Errors\ApiTimeoutError::class, __NAMESPACE__ . '\\ApiTimeoutError');
\class_alias(Errors\UnsupportedContentTypeError::class, __NAMESPACE__ . '\\UnsupportedContentTypeError');
