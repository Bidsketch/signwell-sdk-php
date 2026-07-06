# # CheckboxGroupInfo

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **string** | Checkbox group ID |
**group_name** | **string** | Name of the checkbox group | [optional]
**recipient_id** | **string** | Recipient ID associated with the group | [optional]
**checkbox_ids** | **string[]** | IDs of checkboxes in this group |
**validation** | [**\SignWell\Sdk\Models\CheckboxValidation**](CheckboxValidation.md) |  | [optional]
**required** | **bool** | Whether at least one checkbox must be checked |
**min_value** | **int** | Minimum number of checkboxes to check | [optional]
**max_value** | **int** | Maximum number of checkboxes to check | [optional]
**exact_value** | **int** | Exact number of checkboxes that must be checked | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
