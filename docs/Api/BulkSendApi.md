# SignWell\Sdk\BulkSendApi

All URIs are relative to https://www.signwell.com, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**createBulkSend()**](BulkSendApi.md#createBulkSend) | **POST** /api/v1/bulk_sends | Create Bulk Send |
| [**getBulkSend()**](BulkSendApi.md#getBulkSend) | **GET** /api/v1/bulk_sends/{id} | Get Bulk Send |
| [**getBulkSendCsvTemplate()**](BulkSendApi.md#getBulkSendCsvTemplate) | **GET** /api/v1/bulk_sends/csv_template | Get Bulk Send CSV Template |
| [**getBulkSendDocuments()**](BulkSendApi.md#getBulkSendDocuments) | **GET** /api/v1/bulk_sends/{id}/documents | Get Bulk Send Documents |
| [**listBulkSends()**](BulkSendApi.md#listBulkSends) | **GET** /api/v1/bulk_sends | List Bulk Sendings |
| [**validateBulkSendCsv()**](BulkSendApi.md#validateBulkSendCsv) | **POST** /api/v1/bulk_sends/validate_csv | Validate Bulk Send CSV |


## `createBulkSend()`

```php
createBulkSend($create_bulk_send_request): \SignWell\Sdk\Models\BulkSendCreateResponse
```

Create Bulk Send

Creates a bulk send, and it validates the CSV file before creating the bulk send.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure API key authorization: api_key
$config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKey('X-Api-Key', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// $config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKeyPrefix('X-Api-Key', 'Bearer');


$apiInstance = new SignWell\Sdk\Api\BulkSendApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$create_bulk_send_request = new \SignWell\Sdk\Models\CreateBulkSendRequest(); // \SignWell\Sdk\Models\CreateBulkSendRequest

try {
    $result = $apiInstance->createBulkSend($create_bulk_send_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling BulkSendApi->createBulkSend: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **create_bulk_send_request** | [**\SignWell\Sdk\Models\CreateBulkSendRequest**](../Model/CreateBulkSendRequest.md)|  | |

### Return type

[**\SignWell\Sdk\Models\BulkSendCreateResponse**](../Model/BulkSendCreateResponse.md)

### Authorization

[api_key](../../README.md#api_key)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `getBulkSend()`

```php
getBulkSend($id): \SignWell\Sdk\Models\BulkSendResponse
```

Get Bulk Send

Returns information about the Bulk Send.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure API key authorization: api_key
$config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKey('X-Api-Key', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// $config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKeyPrefix('X-Api-Key', 'Bearer');


$apiInstance = new SignWell\Sdk\Api\BulkSendApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$id = 'id_example'; // string

try {
    $result = $apiInstance->getBulkSend($id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling BulkSendApi->getBulkSend: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **id** | **string**|  | |

### Return type

[**\SignWell\Sdk\Models\BulkSendResponse**](../Model/BulkSendResponse.md)

### Authorization

[api_key](../../README.md#api_key)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `getBulkSendCsvTemplate()`

```php
getBulkSendCsvTemplate($template_ids, $base64): \SplFileObject
```

Get Bulk Send CSV Template

Fetches a CSV template that corresponds to the provided document template IDs. CSV templates are blank CSV files that have columns containing required and optional data that can be sent when creating a bulk send. Fields can be referenced by the field label. Example: [placeholder name]_[field label] could be something like customer_address or signer_company_name (if 'Customer' and 'Signer' were placeholder names for templates set up in SignWell).

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure API key authorization: api_key
$config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKey('X-Api-Key', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// $config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKeyPrefix('X-Api-Key', 'Bearer');


$apiInstance = new SignWell\Sdk\Api\BulkSendApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$template_ids = array('template_ids_example'); // string[]
$base64 = True; // bool

try {
    $result = $apiInstance->getBulkSendCsvTemplate($template_ids, $base64);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling BulkSendApi->getBulkSendCsvTemplate: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **template_ids** | [**string[]**](../Model/string.md)|  | |
| **base64** | **bool**|  | [optional] |

### Return type

**\SplFileObject**

### Authorization

[api_key](../../README.md#api_key)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/octet-stream`, `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `getBulkSendDocuments()`

```php
getBulkSendDocuments($id, $limit, $page): \SignWell\Sdk\Models\BulkSendDocumentsResponse
```

Get Bulk Send Documents

Returns information about the Bulk Send.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure API key authorization: api_key
$config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKey('X-Api-Key', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// $config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKeyPrefix('X-Api-Key', 'Bearer');


$apiInstance = new SignWell\Sdk\Api\BulkSendApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$id = 'id_example'; // string
$limit = 10; // int
$page = 1; // int

try {
    $result = $apiInstance->getBulkSendDocuments($id, $limit, $page);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling BulkSendApi->getBulkSendDocuments: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **id** | **string**|  | |
| **limit** | **int**|  | [optional] [default to 10] |
| **page** | **int**|  | [optional] [default to 1] |

### Return type

[**\SignWell\Sdk\Models\BulkSendDocumentsResponse**](../Model/BulkSendDocumentsResponse.md)

### Authorization

[api_key](../../README.md#api_key)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `listBulkSends()`

```php
listBulkSends($user_email, $limit, $page, $api_application_id): \SignWell\Sdk\Models\BulkSendListResponse
```

List Bulk Sendings

Returns information about the Bulk Send.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure API key authorization: api_key
$config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKey('X-Api-Key', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// $config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKeyPrefix('X-Api-Key', 'Bearer');


$apiInstance = new SignWell\Sdk\Api\BulkSendApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$user_email = 'user_email_example'; // string
$limit = 10; // int
$page = 1; // int
$api_application_id = 'api_application_id_example'; // string

try {
    $result = $apiInstance->listBulkSends($user_email, $limit, $page, $api_application_id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling BulkSendApi->listBulkSends: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **user_email** | **string**|  | [optional] |
| **limit** | **int**|  | [optional] [default to 10] |
| **page** | **int**|  | [optional] [default to 1] |
| **api_application_id** | **string**|  | [optional] |

### Return type

[**\SignWell\Sdk\Models\BulkSendListResponse**](../Model/BulkSendListResponse.md)

### Authorization

[api_key](../../README.md#api_key)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `validateBulkSendCsv()`

```php
validateBulkSendCsv($bulk_send_csv_request): \SignWell\Sdk\Models\BulkSendValidateCsvResponse
```

Validate Bulk Send CSV

Validates a Bulk Send CSV file before creating the Bulk Send. It will check the structure of the CSV and the data it contains, and return any errors found.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure API key authorization: api_key
$config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKey('X-Api-Key', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// $config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKeyPrefix('X-Api-Key', 'Bearer');


$apiInstance = new SignWell\Sdk\Api\BulkSendApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$bulk_send_csv_request = new \SignWell\Sdk\Models\BulkSendCsvRequest(); // \SignWell\Sdk\Models\BulkSendCsvRequest

try {
    $result = $apiInstance->validateBulkSendCsv($bulk_send_csv_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling BulkSendApi->validateBulkSendCsv: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **bulk_send_csv_request** | [**\SignWell\Sdk\Models\BulkSendCsvRequest**](../Model/BulkSendCsvRequest.md)|  | |

### Return type

[**\SignWell\Sdk\Models\BulkSendValidateCsvResponse**](../Model/BulkSendValidateCsvResponse.md)

### Authorization

[api_key](../../README.md#api_key)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
