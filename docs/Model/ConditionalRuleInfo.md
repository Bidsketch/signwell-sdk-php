# # ConditionalRuleInfo

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **string** | Conditional rule ID |
**target_id** | **string** | api_id of the field that owns the rule, or the external_id of a checkbox group when the rule is owned by a group (groups have no api_id) |
**action_type** | **string** | Action applied to action_field_ids when the conditions are met |
**action_field_ids** | **string[]** | api_ids of fields the rule affects when triggered | [optional]
**conditions** | [**\SignWell\Sdk\Models\ConditionalRuleInfoConditionsInner[]**](ConditionalRuleInfoConditionsInner.md) |  | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
