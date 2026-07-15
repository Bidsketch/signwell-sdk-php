# SignWell\Sdk\DocumentApi

All URIs are relative to https://www.signwell.com, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**createDocument()**](DocumentApi.md#createDocument) | **POST** /api/v1/documents | Create Document |
| [**createDocumentFromTemplate()**](DocumentApi.md#createDocumentFromTemplate) | **POST** /api/v1/document_templates/documents | Create Document from Template |
| [**deleteDocument()**](DocumentApi.md#deleteDocument) | **DELETE** /api/v1/documents/{id} | Delete Document |
| [**getCompletedPdf()**](DocumentApi.md#getCompletedPdf) | **GET** /api/v1/documents/{id}/completed_pdf | Completed PDF |
| [**getDocument()**](DocumentApi.md#getDocument) | **GET** /api/v1/documents/{id} | Get Document |
| [**listDocuments()**](DocumentApi.md#listDocuments) | **GET** /api/v1/documents | List Documents |
| [**sendDocument()**](DocumentApi.md#sendDocument) | **POST** /api/v1/documents/{id}/send | Update and Send Document |
| [**sendReminder()**](DocumentApi.md#sendReminder) | **POST** /api/v1/documents/{id}/remind | Send Reminder |
| [**updateRecipients()**](DocumentApi.md#updateRecipients) | **PATCH** /api/v1/documents/{id}/recipients | Update Recipients |


## `createDocument()`

```php
createDocument($document_request): \SignWell\Sdk\Models\DocumentResponse
```

Create Document

Creates and optionally sends a new document for signing. If `draft` is set to true the document will not be sent.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure API key authorization: api_key
$config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKey('X-Api-Key', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// $config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKeyPrefix('X-Api-Key', 'Bearer');


$apiInstance = new SignWell\Sdk\Resources\DocumentApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$document_request = new \SignWell\Sdk\Models\DocumentRequest(); // \SignWell\Sdk\Models\DocumentRequest

try {
    $result = $apiInstance->createDocument($document_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DocumentApi->createDocument: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **document_request** | [**\SignWell\Sdk\Models\DocumentRequest**](../Model/DocumentRequest.md)|  | |

### Return type

[**\SignWell\Sdk\Models\DocumentResponse**](../Model/DocumentResponse.md)

### Authorization

[api_key](../../README.md#api_key)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `createDocumentFromTemplate()`

```php
createDocumentFromTemplate($document_from_template_request): \SignWell\Sdk\Models\DocumentFromTemplateResponse
```

Create Document from Template

Creates and optionally sends a new document for signing. If `draft` is set to true the document will not be sent.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure API key authorization: api_key
$config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKey('X-Api-Key', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// $config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKeyPrefix('X-Api-Key', 'Bearer');


$apiInstance = new SignWell\Sdk\Resources\DocumentApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$document_from_template_request = new \SignWell\Sdk\Models\DocumentFromTemplateRequest(); // \SignWell\Sdk\Models\DocumentFromTemplateRequest

try {
    $result = $apiInstance->createDocumentFromTemplate($document_from_template_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DocumentApi->createDocumentFromTemplate: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **document_from_template_request** | [**\SignWell\Sdk\Models\DocumentFromTemplateRequest**](../Model/DocumentFromTemplateRequest.md)|  | |

### Return type

[**\SignWell\Sdk\Models\DocumentFromTemplateResponse**](../Model/DocumentFromTemplateResponse.md)

### Authorization

[api_key](../../README.md#api_key)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `deleteDocument()`

```php
deleteDocument($id)
```

Delete Document

Deletes a document. Deleting a document will also cancel document signing (if in progress).  Supply the unique document ID from either a Create Document request or document page URL.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure API key authorization: api_key
$config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKey('X-Api-Key', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// $config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKeyPrefix('X-Api-Key', 'Bearer');


$apiInstance = new SignWell\Sdk\Resources\DocumentApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$id = 'id_example'; // string

try {
    $apiInstance->deleteDocument($id);
} catch (Exception $e) {
    echo 'Exception when calling DocumentApi->deleteDocument: ', $e->getMessage(), PHP_EOL;
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

## `getCompletedPdf()`

```php
getCompletedPdf($id, $url_only, $audit_page, $file_format): \SignWell\Sdk\Models\CompletedPdfResponse
```

Completed PDF

Gets a completed document PDF or ZIP file. Supply the unique document ID from either a document creation request or document page URL.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure API key authorization: api_key
$config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKey('X-Api-Key', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// $config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKeyPrefix('X-Api-Key', 'Bearer');


$apiInstance = new SignWell\Sdk\Resources\DocumentApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$id = 'id_example'; // string
$url_only = false; // bool
$audit_page = true; // bool
$file_format = new \SignWell\Sdk\Models\\SignWell\Sdk\Models\FileFormat(); // \SignWell\Sdk\Models\FileFormat

try {
    $result = $apiInstance->getCompletedPdf($id, $url_only, $audit_page, $file_format);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DocumentApi->getCompletedPdf: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **id** | **string**|  | |
| **url_only** | **bool**|  | [optional] [default to false] |
| **audit_page** | **bool**|  | [optional] [default to true] |
| **file_format** | [**\SignWell\Sdk\Models\FileFormat**](../Model/.md)|  | [optional] |

### Return type

[**\SignWell\Sdk\Models\CompletedPdfResponse**](../Model/CompletedPdfResponse.md)

### Authorization

[api_key](../../README.md#api_key)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `getDocument()`

```php
getDocument($id): \SignWell\Sdk\Models\DocumentResponse
```

Get Document

Returns a document and all associated document data. Supply the unique document ID from either a document creation request or Document page URL.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure API key authorization: api_key
$config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKey('X-Api-Key', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// $config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKeyPrefix('X-Api-Key', 'Bearer');


$apiInstance = new SignWell\Sdk\Resources\DocumentApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$id = 'id_example'; // string

try {
    $result = $apiInstance->getDocument($id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DocumentApi->getDocument: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **id** | **string**|  | |

### Return type

[**\SignWell\Sdk\Models\DocumentResponse**](../Model/DocumentResponse.md)

### Authorization

[api_key](../../README.md#api_key)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `listDocuments()`

```php
listDocuments($page, $limit, $query): \SignWell\Sdk\Models\DocumentListResponse
```

List Documents

Returns a paginated list of documents for the authenticated account.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure API key authorization: api_key
$config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKey('X-Api-Key', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// $config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKeyPrefix('X-Api-Key', 'Bearer');


$apiInstance = new SignWell\Sdk\Resources\DocumentApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$page = 1; // int
$limit = 10; // int
$query = 'query_example'; // string | Raw API filter query. Use AND between filters, for example: \"name:Classic AND status:completed\".

try {
    $result = $apiInstance->listDocuments($page, $limit, $query);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DocumentApi->listDocuments: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **page** | **int**|  | [optional] [default to 1] |
| **limit** | **int**|  | [optional] [default to 10] |
| **query** | **string**| Raw API filter query. Use AND between filters, for example: \&quot;name:Classic AND status:completed\&quot;. | [optional] |

### Return type

[**\SignWell\Sdk\Models\DocumentListResponse**](../Model/DocumentListResponse.md)

### Authorization

[api_key](../../README.md#api_key)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `sendDocument()`

```php
sendDocument($id, $update_document_and_send_request): \SignWell\Sdk\Models\DocumentResponse
```

Update and Send Document

Updates a draft document and sends it to be signed by recipients.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure API key authorization: api_key
$config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKey('X-Api-Key', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// $config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKeyPrefix('X-Api-Key', 'Bearer');


$apiInstance = new SignWell\Sdk\Resources\DocumentApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$id = 'id_example'; // string
$update_document_and_send_request = new \SignWell\Sdk\Models\UpdateDocumentAndSendRequest(); // \SignWell\Sdk\Models\UpdateDocumentAndSendRequest

try {
    $result = $apiInstance->sendDocument($id, $update_document_and_send_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DocumentApi->sendDocument: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **id** | **string**|  | |
| **update_document_and_send_request** | [**\SignWell\Sdk\Models\UpdateDocumentAndSendRequest**](../Model/UpdateDocumentAndSendRequest.md)|  | |

### Return type

[**\SignWell\Sdk\Models\DocumentResponse**](../Model/DocumentResponse.md)

### Authorization

[api_key](../../README.md#api_key)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `sendReminder()`

```php
sendReminder($id, $send_reminder_request)
```

Send Reminder

Sends a reminder email to recipients that have not signed yet.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure API key authorization: api_key
$config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKey('X-Api-Key', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// $config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKeyPrefix('X-Api-Key', 'Bearer');


$apiInstance = new SignWell\Sdk\Resources\DocumentApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$id = 'id_example'; // string
$send_reminder_request = new \SignWell\Sdk\Models\SendReminderRequest(); // \SignWell\Sdk\Models\SendReminderRequest

try {
    $apiInstance->sendReminder($id, $send_reminder_request);
} catch (Exception $e) {
    echo 'Exception when calling DocumentApi->sendReminder: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **id** | **string**|  | |
| **send_reminder_request** | [**\SignWell\Sdk\Models\SendReminderRequest**](../Model/SendReminderRequest.md)|  | |

### Return type

void (empty response body)

### Authorization

[api_key](../../README.md#api_key)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `updateRecipients()`

```php
updateRecipients($id, $update_recipients_request): \SignWell\Sdk\Models\DocumentResponse
```

Update Recipients

Updates one or more recipients on a document that has already been sent. Only recipients who have not started signing may be updated. Recipient IDs must be retrieved from the Get Document response. Allowed document statuses: sent, viewed, pending, bounced. For non-embedded documents, updated recipients will receive a new notification email. For embedded signing documents, email behavior follows each recipient's send_email setting.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure API key authorization: api_key
$config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKey('X-Api-Key', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// $config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKeyPrefix('X-Api-Key', 'Bearer');


$apiInstance = new SignWell\Sdk\Resources\DocumentApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$id = 'id_example'; // string
$update_recipients_request = new \SignWell\Sdk\Models\UpdateRecipientsRequest(); // \SignWell\Sdk\Models\UpdateRecipientsRequest

try {
    $result = $apiInstance->updateRecipients($id, $update_recipients_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DocumentApi->updateRecipients: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **id** | **string**|  | |
| **update_recipients_request** | [**\SignWell\Sdk\Models\UpdateRecipientsRequest**](../Model/UpdateRecipientsRequest.md)|  | |

### Return type

[**\SignWell\Sdk\Models\DocumentResponse**](../Model/DocumentResponse.md)

### Authorization

[api_key](../../README.md#api_key)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
