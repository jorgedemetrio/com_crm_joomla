# Sentinel's Journal

## 2024-05-23 - [Insecure File Upload Fail-Open]
**Vulnerability:** The file upload validation logic in `CrmModelImportArquivo` skipped the MIME type check entirely if the `finfo_open` function was not available on the server. This "fail-open" behavior meant that on servers without `fileinfo`, an attacker could bypass the extension check (if they found a way) or upload malicious content in a file with a valid extension (e.g., HTML in a .csv file).
**Learning:** Security controls must follow the "Fail Secure" principle. If a security check cannot be performed (due to missing dependencies or errors), the action should be blocked, not allowed.
**Prevention:** Initialize validation flags to `false`. Ensure that the code explicitly confirms the check passed. Provide fallbacks (like `mime_content_type`) but ultimately deny the action if no validation method is available.
