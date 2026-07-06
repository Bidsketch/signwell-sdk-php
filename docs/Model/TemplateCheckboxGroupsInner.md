# # TemplateCheckboxGroupsInner

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**group_name** | **string** | A unique identifier for the checkbox group. |
**placeholder_id** | **string** | The recipient ID associated with the checkbox group. |
**checkbox_ids** | **string[]** |  |
**validation** | [**\SignWell\Sdk\Models\CheckboxValidation**](CheckboxValidation.md) |  | [optional]
**required** | **bool** | Whether the group must be completed by the recipient. Defaults to false. | [optional] [default to false]
**min_value** | **int** | The minimum number of checkboxes that must be checked in the group. (Only for validation: minimum and range) | [optional]
**max_value** | **int** | The maximum number of checkboxes that can be checked in the group. (Only for validation: maximum and range) | [optional]
**exact_value** | **int** | The exact number of checkboxes that must be checked in the group. (Only for validation: exact) | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
