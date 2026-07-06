# # CreateBulkSendRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**template_ids** | **string[]** | Unique identifiers for a list of templates. |
**bulk_send_csv** | **string** | A RFC 4648 base64 string of the template CSV file to be validated. |
**skip_row_errors** | **bool** | Whether to skip errors in the rows. Defaults to &#x60;false&#x60;. | [optional] [default to false]
**api_application_id** | **string** | Unique identifier for API Application settings to use. API Applications are optional and mainly used when isolating OAuth apps or for more control over embedded API settings | [optional]
**name** | **string** | The name of the Bulk Send. Will be used as the document name for each of the documents. | [optional]
**subject** | **string** | Email subject for the signature request that recipients will see. Defaults to the default system subject or a template subject. | [optional]
**message** | **string** | Email message for the signature request that recipients will see. Defaults to the default system message or a template message. | [optional]
**apply_signing_order** | **bool** | When set to &#x60;true&#x60; recipients will sign one at a time in the order of the &#x60;recipients&#x60; collection of this request. | [optional] [default to false]
**custom_requester_name** | **string** | Sets the custom requester name for the document. When set, this is the name used for all email communications, signing notifications, and in the audit file. | [optional]
**custom_requester_email** | **string** | Sets the custom requester email for the document. When set, this is the email used for all email communications, signing notifications, and in the audit file. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
