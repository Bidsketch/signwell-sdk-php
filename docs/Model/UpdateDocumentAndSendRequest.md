# # UpdateDocumentAndSendRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**test_mode** | **bool** | Set to &#x60;true&#x60; to enable Test Mode. Documents created with Test Mode do not count towards API billing and are not legally binding. Defaults to &#x60;false&#x60; | [optional] [default to false]
**name** | **string** | The name of the document. | [optional]
**subject** | **string** | Email subject for the signature request that recipients will see. Defaults to the default system subject or a template subject (if the document is created from a template). | [optional]
**message** | **string** | Email message for the signature request that recipients will see. Defaults to the default system message or a template message (if the document is created from a template). | [optional]
**expires_in** | **int** | Number of days before the signature request expires. Defaults to the account expiration setting or template expiration (if the document is created from a template). | [optional]
**reminders** | **bool** | Whether to send signing reminders to recipients. Reminders are sent on day 3, day 6, and day 10 if set to &#x60;true&#x60;. Defaults to &#x60;true&#x60;. | [optional] [default to true]
**apply_signing_order** | **bool** | When set to &#x60;true&#x60; recipients will sign one at a time in the order of the &#x60;recipients&#x60; collection of this request. | [optional] [default to false]
**api_application_id** | **string** | Unique identifier for API Application settings to use. API Applications are optional and mainly used when isolating OAuth apps or for more control over embedded API settings | [optional]
**embedded_signing** | **bool** | When set to &#x60;true&#x60; it enables embedded signing in your website/web application. Embedded functionality works with an iFrame and email authentication is disabled. :embedded_signinig defaults to &#x60;false&#x60;. | [optional] [default to false]
**embedded_signing_notifications** | **bool** | On embedding signing, document owners (and CC&#39;d contacts) do not get a notification email when documents have been completed. Setting this param to &#x60;true&#x60; will send out those final completed notifications. Default is &#x60;false&#x60; | [optional] [default to false]
**custom_requester_name** | **string** | Sets the custom requester name for the document. When set, this is the name used for all email communications, signing notifications, and in the audit file. | [optional]
**custom_requester_email** | **string** | Sets the custom requester email for the document. When set, this is the email used for all email communications, signing notifications, and in the audit file. | [optional]
**redirect_url** | **string** | A URL that recipients are redirected to after successfully signing a document. | [optional]
**allow_decline** | **bool** | Whether to allow recipients the option to decline signing a document. If multiple signers are involved in a document, any single recipient can cancel the entire document signing process by declining to sign. | [optional] [default to true]
**allow_reassign** | **bool** | In some cases a signer is not the right person to sign and may need to reassign their signing responsibilities to another person. This feature allows them to reassign the document to someone else. | [optional] [default to true]
**decline_redirect_url** | **string** | A URL that recipients are redirected to if the document is declined. | [optional]
**metadata** | **array<string,string>** | Optional key-value data that can be associated with the document. If set, will be available every time the document data is returned. | [optional]
**labels** | [**\SignWell\Sdk\Models\LabelRequest[]**](LabelRequest.md) | Labels can be used to organize documents in a way that can make it easy to find using the document search in SignWell. A document can have multiple labels. Updating labels on a document will replace any existing labels for that document. | [optional]
**checkbox_groups** | [**\SignWell\Sdk\Models\CheckboxGroupsInner[]**](CheckboxGroupsInner.md) | Checkbox fields that are placed on a document can be grouped with selection requirements. At least 2 checkbox fields in an array of fields must be assigned to the same recipient. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
