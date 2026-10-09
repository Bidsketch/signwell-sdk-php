# # SendReminderRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**recipients** | [**\SignWell\Sdk\Models\ReminderRecipientsInner[]**](ReminderRecipientsInner.md) | Optional list of recipients within the document to remind. If none are specified, all recipients that have not signed yet are reminded. Each recipient is reminded through their own delivery method: email-only recipients get a reminder email, SMS-only recipients get a reminder text, and email_and_sms recipients get both. If the workspace has insufficient SMS credit, only the SMS reminder is skipped and the sender is notified once; the email reminder (if applicable) still goes out. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
