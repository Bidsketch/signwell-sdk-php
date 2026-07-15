# SignWell\Sdk\TemplateApi

All URIs are relative to https://www.signwell.com, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**createTemplate()**](TemplateApi.md#createTemplate) | **POST** /api/v1/document_templates | Create Template |
| [**deleteTemplate()**](TemplateApi.md#deleteTemplate) | **DELETE** /api/v1/document_templates/{id} | Delete Template |
| [**getTemplate()**](TemplateApi.md#getTemplate) | **GET** /api/v1/document_templates/{id} | Get Template |
| [**listTemplates()**](TemplateApi.md#listTemplates) | **GET** /api/v1/document_templates | List Templates |
| [**updateTemplate()**](TemplateApi.md#updateTemplate) | **PUT** /api/v1/document_templates/{id} | Update Template |


## `createTemplate()`

```php
createTemplate($document_template_request): \SignWell\Sdk\Models\DocumentTemplateResponse
```

Create Template

Creates a new template.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure API key authorization: api_key
$config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKey('X-Api-Key', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// $config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKeyPrefix('X-Api-Key', 'Bearer');


$apiInstance = new SignWell\Sdk\Resources\TemplateApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$document_template_request = new \SignWell\Sdk\Models\DocumentTemplateRequest(); // \SignWell\Sdk\Models\DocumentTemplateRequest

try {
    $result = $apiInstance->createTemplate($document_template_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling TemplateApi->createTemplate: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **document_template_request** | [**\SignWell\Sdk\Models\DocumentTemplateRequest**](../Model/DocumentTemplateRequest.md)|  | |

### Return type

[**\SignWell\Sdk\Models\DocumentTemplateResponse**](../Model/DocumentTemplateResponse.md)

### Authorization

[api_key](../../README.md#api_key)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `deleteTemplate()`

```php
deleteTemplate($id)
```

Delete Template

Deletes a template. Supply the unique template ID from either a Create Template request or template page URL.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure API key authorization: api_key
$config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKey('X-Api-Key', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// $config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKeyPrefix('X-Api-Key', 'Bearer');


$apiInstance = new SignWell\Sdk\Resources\TemplateApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$id = 'id_example'; // string

try {
    $apiInstance->deleteTemplate($id);
} catch (Exception $e) {
    echo 'Exception when calling TemplateApi->deleteTemplate: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **id** | **string**|  | |

### Return type

void (empty response body)

### Authorization

[api_key](../../README.md#api_key)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `getTemplate()`

```php
getTemplate($id): \SignWell\Sdk\Models\DocumentTemplateResponse
```

Get Template

Returns a template and all associated template data. Supply the unique template ID from either a Create Template request or template page URL.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure API key authorization: api_key
$config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKey('X-Api-Key', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// $config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKeyPrefix('X-Api-Key', 'Bearer');


$apiInstance = new SignWell\Sdk\Resources\TemplateApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$id = 'id_example'; // string

try {
    $result = $apiInstance->getTemplate($id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling TemplateApi->getTemplate: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **id** | **string**|  | |

### Return type

[**\SignWell\Sdk\Models\DocumentTemplateResponse**](../Model/DocumentTemplateResponse.md)

### Authorization

[api_key](../../README.md#api_key)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `listTemplates()`

```php
listTemplates($page, $limit, $query): \SignWell\Sdk\Models\DocumentTemplateListResponse
```

List Templates

Returns a paginated list of templates for the authenticated account.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure API key authorization: api_key
$config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKey('X-Api-Key', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// $config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKeyPrefix('X-Api-Key', 'Bearer');


$apiInstance = new SignWell\Sdk\Resources\TemplateApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$page = 1; // int
$limit = 10; // int
$query = 'query_example'; // string | Raw API filter query. Use AND between filters, for example: \"name:Classic AND status:Available\".

try {
    $result = $apiInstance->listTemplates($page, $limit, $query);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling TemplateApi->listTemplates: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **page** | **int**|  | [optional] [default to 1] |
| **limit** | **int**|  | [optional] [default to 10] |
| **query** | **string**| Raw API filter query. Use AND between filters, for example: \&quot;name:Classic AND status:Available\&quot;. | [optional] |

### Return type

[**\SignWell\Sdk\Models\DocumentTemplateListResponse**](../Model/DocumentTemplateListResponse.md)

### Authorization

[api_key](../../README.md#api_key)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `updateTemplate()`

```php
updateTemplate($id, $document_template_update_request): \SignWell\Sdk\Models\DocumentTemplateResponse
```

Update Template

Updates an existing template.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure API key authorization: api_key
$config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKey('X-Api-Key', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// $config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKeyPrefix('X-Api-Key', 'Bearer');


$apiInstance = new SignWell\Sdk\Resources\TemplateApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$id = 'id_example'; // string
$document_template_update_request = new \SignWell\Sdk\Models\DocumentTemplateUpdateRequest(); // \SignWell\Sdk\Models\DocumentTemplateUpdateRequest

try {
    $result = $apiInstance->updateTemplate($id, $document_template_update_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling TemplateApi->updateTemplate: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **id** | **string**|  | |
| **document_template_update_request** | [**\SignWell\Sdk\Models\DocumentTemplateUpdateRequest**](../Model/DocumentTemplateUpdateRequest.md)|  | |

### Return type

[**\SignWell\Sdk\Models\DocumentTemplateResponse**](../Model/DocumentTemplateResponse.md)

### Authorization

[api_key](../../README.md#api_key)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
