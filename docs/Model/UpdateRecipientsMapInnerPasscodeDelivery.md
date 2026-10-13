# # UpdateRecipientsMapInnerPasscodeDelivery

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**enabled** | **bool** | Enable or disable passcode delivery. Cannot be set to &#x60;false&#x60; on a 21 CFR Part 11 document: it returns a 422. | [optional]
**methods** | **string[]** | Delivery methods. Defaults to [\&quot;email\&quot;]. | [optional]
**expire_after_access** | **bool** | Clear passcode after successful authentication. If passcode_delivery is also enabled, a new passcode is auto-generated on the next send request. Otherwise the recipient will no longer need a passcode. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
