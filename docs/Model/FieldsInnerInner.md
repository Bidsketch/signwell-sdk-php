# # FieldsInnerInner

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**x** | **float** | Horizontal value in the coordinates of the field (in pixels). Coordinates are specific to the page where fields are located. |
**y** | **float** | Vertical value in the coordinates of the field (in pixels). Coordinates are specific to the page where fields are located. |
**page** | **int** | The page number within the file. If the page does not exist within the file then the field won&#39;t be created. |
**recipient_id** | **string** | Unique identifier of the recipient assigned to the field. Recipients assigned to fields will be the only ones that will see and be able to complete those fields. |
**type** | [**\SignWell\Sdk\Models\FieldType**](FieldType.md) |  |
**required** | **bool** | Whether the field must be completed by the recipient. Defaults to &#x60;true&#x60; except for checkbox type fields. | [optional] [default to true]
**label** | **string** | Text and Date fields only: label that is displayed when the field is empty. | [optional]
**value** | [**\SignWell\Sdk\Models\AdditionalFieldsInnerInnerValue**](AdditionalFieldsInnerInnerValue.md) |  | [optional]
**api_id** | **string** | Unique identifier of the field. Useful when needing to reference specific field values or update a document and its fields. | [optional]
**name** | **string** | Checkbox fields only. At least 2 checkbox fields in an array of fields must be assigned to the same recipient and grouped with selection requirements. | [optional]
**validation** | [**\SignWell\Sdk\Models\TextValidation**](TextValidation.md) |  | [optional]
**fixed_width** | **bool** | Text fields only: whether the field width will stay fixed and text will display in multiple lines, rather than one long line. If set to &#x60;false&#x60; the field width will automatically grow horizontally to fit text on one line. Defaults to &#x60;false&#x60;. | [optional] [default to false]
**lock_sign_date** | **bool** | Date fields only: makes fields readonly and automatically populates with the date the recipient signed. Defaults to &#x60;false&#x60;. | [optional] [default to false]
**date_format** | [**\SignWell\Sdk\Models\DateFormat**](DateFormat.md) |  | [optional]
**height** | **float** | Height of the field (in pixels). Maximum height varies by field type: Signature/Initials (200px), others (74px). When using text tags if the height is greater than the maximum height, the height will be set to the maximum height. | [optional]
**width** | **float** | Width of the field (in pixels). For text fields, width will auto-grow unless &#x60;fixed_width&#x60; is true. | [optional]
**options** | [**\SignWell\Sdk\Models\DropdownOption[]**](DropdownOption.md) | Array of dropdown options (for dropdown/select fields only) | [optional]
**default_option** | **string** | Default selected option (for dropdown/select fields only) | [optional]
**allow_other** | **bool** | Whether to allow \&quot;Other\&quot; option with text input (for dropdown/select fields only) | [optional] [default to false]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
