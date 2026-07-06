# # DocumentTemplateResponse

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **string** |  |
**api_application_id** | **string** |  | [optional]
**requester_email_address** | **string** |  | [optional]
**custom_requester_name** | **string** |  | [optional]
**custom_requester_email** | **string** |  | [optional]
**name** | **string** |  | [optional]
**subject** | **string** |  | [optional]
**message** | **string** |  | [optional]
**metadata** | **array<string,string>** |  | [optional]
**created_at** | **\DateTime** |  | [optional]
**updated_at** | **\DateTime** |  | [optional]
**placeholders** | [**\SignWell\Sdk\Models\DocumentTemplateResponsePlaceholdersInner[]**](DocumentTemplateResponsePlaceholdersInner.md) |  | [optional]
**copied_placeholders** | [**\SignWell\Sdk\Models\DocumentTemplateResponseCopiedPlaceholdersInner[]**](DocumentTemplateResponseCopiedPlaceholdersInner.md) |  | [optional]
**status** | **string** |  | [optional]
**reminders** | **bool** |  | [optional]
**archived** | **bool** |  | [optional]
**embedded_edit_url** | **string** |  | [optional]
**template_link** | **string** |  | [optional]
**template_id** | **string** |  | [optional]
**apply_signing_order** | **bool** |  | [optional]
**redirect_url** | **string** |  | [optional]
**decline_redirect_url** | **string** |  | [optional]
**language** | **string** |  | [optional]
**expires_in** | **int** |  | [optional]
**files** | [**\SignWell\Sdk\Models\FileInfo[]**](FileInfo.md) |  | [optional]
**fields** | **\SignWell\Sdk\Models\DocumentResponseFieldsInnerInner[][]** |  | [optional]
**allow_decline** | **bool** |  | [optional]
**allow_reassign** | **bool** |  | [optional]
**labels** | [**\SignWell\Sdk\Models\LabelInfo[]**](LabelInfo.md) |  | [optional]
**checkbox_groups** | [**\SignWell\Sdk\Models\CheckboxGroupInfo[]**](CheckboxGroupInfo.md) |  | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
