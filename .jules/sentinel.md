# Sentinel's Journal

## 2024-05-23 - [Insecure File Upload Fail-Open]
**Vulnerability:** The file upload validation logic in `CrmModelImportArquivo` skipped the MIME type check entirely if the `finfo_open` function was not available on the server. This "fail-open" behavior meant that on servers without `fileinfo`, an attacker could bypass the extension check (if they found a way) or upload malicious content in a file with a valid extension (e.g., HTML in a .csv file).
**Learning:** Security controls must follow the "Fail Secure" principle. If a security check cannot be performed (due to missing dependencies or errors), the action should be blocked, not allowed.
**Prevention:** Initialize validation flags to `false`. Ensure that the code explicitly confirms the check passed. Provide fallbacks (like `mime_content_type`) but ultimately deny the action if no validation method is available.

## 2024-05-24 - [CSRF in GET State Change]
**Vulnerability:** The `CrmControllerOptout::unsubscribe` method allowed unsubscribing a user via a simple GET request without any token verification. This exposed the application to CSRF attacks where an attacker could unsubscribe victims by tricking them into clicking a link or loading an image.
**Learning:** Actions that change state (create, update, delete) must never be performed via GET requests. GET requests should be idempotent and safe. Legacy code often mixes "display" and "action" in the same controller method.
**Prevention:** Implement a "Verify Intent" pattern. Intercept GET requests to sensitive actions and redirect them to a confirmation view (form). The actual action must be performed via a POST request protected by a CSRF token (`JSession::checkToken`).
