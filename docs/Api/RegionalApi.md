# SignWell\Sdk\RegionalApi

All URIs are relative to https://www.signwell.com, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**getNom151Certificate()**](RegionalApi.md#getNom151Certificate) | **GET** /api/v1/documents/{id}/nom151_certificate | MX – NOM-151 Certificate |


## `getNom151Certificate()`

```php
getNom151Certificate($id, $url_only, $object_only): \SignWell\Sdk\Models\Nom151UrlResponse
```

MX – NOM-151 Certificate

Download NOM-151 certificate for a completed document. Returns a ZIP file, download URL, or raw certificate data based on query parameters.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure API key authorization: api_key
$config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKey('X-Api-Key', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// $config = SignWell\Sdk\Configuration::getDefaultConfiguration()->setApiKeyPrefix('X-Api-Key', 'Bearer');


$apiInstance = new SignWell\Sdk\Resources\RegionalApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$id = 'id_example'; // string
$url_only = false; // bool | If true, returns JSON with download URL instead of downloading the file
$object_only = false; // bool

try {
    $result = $apiInstance->getNom151Certificate($id, $url_only, $object_only);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling RegionalApi->getNom151Certificate: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **id** | **string**|  | |
| **url_only** | **bool**| If true, returns JSON with download URL instead of downloading the file | [optional] [default to false] |
| **object_only** | **bool**|  | [optional] [default to false] |

### Return type

[**\SignWell\Sdk\Models\Nom151UrlResponse**](../Model/Nom151UrlResponse.md)

### Authorization

[api_key](../../README.md#api_key)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
