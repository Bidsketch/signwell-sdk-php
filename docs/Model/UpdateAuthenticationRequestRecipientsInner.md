# # UpdateAuthenticationRequestRecipientsInner

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **string** | A unique identifier that you will give to each recipient. We recommend numbering sequentially from 1 to X. IDs are required for associating recipients to fields and more. |
**passcode** | **string** | Updated passcode for the recipient. If set, the signer will be required to enter the passcode before viewing and completing the document. On a 21 CFR Part 11 document SignWell generates the passcode, so a sender-defined passcode returns a 422. | [optional]
**passcode_delivery** | [**\SignWell\Sdk\Models\UpdateRecipientsMapInnerPasscodeDelivery**](UpdateRecipientsMapInnerPasscodeDelivery.md) |  | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
