# # EmbeddedBrandingRequestCompanyLogo

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**file_url** | **string** | Public &#x60;http&#x60; or &#x60;https&#x60; URL of the logo image. The value has to parse as one — anything else returns a &#x60;422&#x60;. SignWell never requests the URL when the document is created, so it is not checked for reachability or for what it points at. If the URL turns out to be unreachable, slow, or not a usable image when the page loads, the page falls back to the no-logo experience rather than erroring. | [optional]
**file_base64** | **string** | RFC 4648 Base64 string of the logo image. The 300 KB limit applies to the decoded image, not to the string you send, so a valid string runs about a third larger. The decoded image must be one of image/jpeg, image/png, image/gif, image/tiff, image/webp, image/svg+xml, image/heic, image/heif, image/avif. A payload that cannot be decoded, is not one of those formats, or is over the size limit returns a &#x60;422&#x60;. A payload that is accepted here but still fails to render when the page loads falls back to the no-logo experience. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
