# Changelog

## 0.2.0 — Unreleased

### Added

- Support the current documented SMS delivery fields, conditional rules and decisions, and embedded signing/branding options.
- Expose 21 CFR Part 11 indicators in document responses and updated passcode-delivery settings.
- Add the document authentication update endpoint.

### Changed

- Refresh generated resources, request/response models, and API documentation from the current published SignWell API.
- Regenerate with OpenAPI Generator 7.26.0 while preserving existing minimum runtimes.
- Standardize the default SDK User-Agent; callers can continue to configure their own value.
- Accept absent or null FileInfo.name values in document responses.

### Fixed

- Preserve primitive union values when reading responses, including checkbox `t`/`f`, booleans, numbers, and numeric-looking strings. Dropdown options accept both strings and detailed objects.

### Dependencies

- Update Guzzle to 8.2 and PSR-7 to 3.1, preserving PHP 8.2 support. Adapt JSON encoding and transport exceptions to the new APIs.
- Retain Laravel 12 and PHPUnit 11 as the newest lines compatible with PHP 8.2.

### Migration

- Applications that pin Guzzle, Guzzle Promises, or Guzzle PSR-7 directly must allow Guzzle 8, Promises 3, and PSR-7 3. Custom headers must use string values.

## 0.1.0 - Unreleased

- Initial PHP SDK release for the SignWell API.
- Includes vanilla PHP client helpers, generated API resources, Laravel service-provider bindings, embedded signing helpers, and webhook verification utilities.
