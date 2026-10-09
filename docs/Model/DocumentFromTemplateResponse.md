# # DocumentFromTemplateResponse

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**cfr_part11** | **bool** | Whether 21 CFR Part 11 controls apply to this document. Read-only: the value is taken from the workspace setting when the document is created and never changes afterwards. | [optional]
**cfr_part11_signing_mode** | **string** | The signing mode of a 21 CFR Part 11 document: streamlined (one final authentication) or per_signature (authentication at every signature and initials field). Null when Part 11 does not apply. | [optional]
**test_mode** | **bool** |  |
**id** | **string** |  |
**template_id** | **string** |  | [optional]
**template_ids** | **string[]** |  | [optional]
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
**recipients** | [**\SignWell\Sdk\Models\DocumentFromTemplateResponseRecipientsInner[]**](DocumentFromTemplateResponseRecipientsInner.md) |  | [optional]
**status** | **string** | Possible values: Draft, Created, Sending, Sent, Pending, Viewed, Completed, Manually completed, Declined, Canceled, Bounced, Blocked, Error, Expired | [optional]
**reminders** | **bool** |  | [optional]
**archived** | **bool** |  | [optional]
**embedded** | [**\SignWell\Sdk\Models\EmbeddedBranding**](EmbeddedBranding.md) |  | [optional]
**embedded_signing** | **bool** |  | [optional]
**embedded_edit_url** | **string** |  | [optional]
**embedded_preview_url** | **string** |  | [optional]
**apply_signing_order** | **bool** |  | [optional]
**redirect_url** | **string** |  | [optional]
**decline_redirect_url** | **string** |  | [optional]
**language** | **string** |  | [optional]
**expires_in** | **int** |  | [optional]
**decline_message** | **string** |  | [optional]
**error_message** | **string** |  | [optional]
**embedded_signing_notifications** | **bool** |  | [optional]
**attachment_requests** | [**\SignWell\Sdk\Models\DocumentResponseAttachmentRequestsInner[]**](DocumentResponseAttachmentRequestsInner.md) |  | [optional]
**files** | [**\SignWell\Sdk\Models\FileInfo[]**](FileInfo.md) |  | [optional]
**copied_contacts** | [**\SignWell\Sdk\Models\CopiedContactInfo[]**](CopiedContactInfo.md) |  | [optional]
**fields** | **\SignWell\Sdk\Models\DocumentFromTemplateResponseFieldsInnerInner[][]** |  | [optional]
**allow_decline** | **bool** |  | [optional]
**allow_reassign** | **bool** |  | [optional]
**labels** | [**\SignWell\Sdk\Models\LabelInfo[]**](LabelInfo.md) |  | [optional]
**checkbox_groups** | [**\SignWell\Sdk\Models\CheckboxGroupInfo[]**](CheckboxGroupInfo.md) |  | [optional]
**conditional_rules** | [**\SignWell\Sdk\Models\ConditionalRuleInfo[]**](ConditionalRuleInfo.md) |  | [optional]
**conditional_logic_decisions** | [**\SignWell\Sdk\Models\ConditionalLogicDecisionInfo[]**](ConditionalLogicDecisionInfo.md) |  | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
