# Sentinel's Journal

## 2025-05-23 - [Joomla 3 Component Security]
**Vulnerability:** Missing Authorization (Broken Access Control) in custom controller tasks (`process`, `doImport`).
**Learning:** Custom tasks in `JControllerForm` derivatives do not automatically inherit standard CRUD permissions. Explicit ACL checks (`authorise`) are mandatory to prevent unauthorized execution of business logic.
**Prevention:** Always verify user permissions at the start of any public or custom controller method, especially those performing sensitive operations like data import.

## 2025-05-23 - [Joomla 3 Component Security]
**Vulnerability:** Import features modifying user data to prevent CSV Injection on import (prepending `'`).
**Learning:** Preventing Formula Injection on *import* (write-time) permanently alters database data (e.g., user names starting with `@` become `'@`). This protects the admin export later, but corrupts the data for other uses (emails, integrations).
**Prevention:** Validation/Sanitization should ideally happen on *output* (read-time) or only invalid characters should be stripped, rather than escaping via modification, unless specifically required. However, for this project, the requirement was explicit to fail secure for CSV injection on import.

## 2025-05-23 - [Joomla 3 Form Security]
**Vulnerability:** Missing Input Sanitization and Length Validation in XML Forms (`com_crm/administrator/forms`).
**Learning:** Joomla XML forms define validation rules but default to raw input if `filter` is omitted. Explicitly adding `filter="string"` prevents Stored XSS by stripping HTML tags at the controller level before model binding.
**Prevention:** Audit all `<field type="text">` elements in XML forms to ensure they have `filter="string"` (or appropriate filter) and `maxlength` attributes matching the database schema.

## 2025-05-23 - [Input Validation - Allow-listing]
**Vulnerability:** Loose type definition in XML forms allowing invalid ENUM values.
**Learning:** Fields defined as `ENUM` in the database were exposed as free-text `<field type="text">` in Joomla XML forms. This allowed users to submit invalid state strings (e.g., for `status_job`), potentially causing application logic errors or bypassing validation flows.
**Prevention:** Always map Database `ENUM` fields to `<field type="list">` with explicit `<option>` values in the XML form definition to enforce strict allow-listing at the input layer.

## 2025-05-23 - Router ID Validation
**Vulnerability:** Insecure Direct Object Reference / ID Truncation
**Learning:** Joomla's legacy `(int)` casting in routers silently corrupts UUIDs (truncating them to 0) and potentially allows "dirty" IDs (e.g., `123-junk`).
**Prevention:** Use strict validation (Numeric OR UUID Regex) instead of casting to `(int)` when handling IDs in `ParseRoute`, especially for components supporting UUIDs.

## 2026-01-28 - [Joomla 3 Form Security - URL]
**Vulnerability:** `type="url"` fields in XML forms using `filter="string"` allows Javascript URI schemes (`javascript:alert(1)`).
**Learning:** `filter="string"` only strips HTML tags but preserves the content. It does not validate or sanitize the protocol. `filter="url"` is required to sanitize the URL (checking protocol, escaping).
**Prevention:** Always pair `type="url"` with `filter="url"` in Joomla XML forms to prevent Stored XSS via dangerous protocols.
