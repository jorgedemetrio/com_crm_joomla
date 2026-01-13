# Sentinel's Journal

## 2025-05-23 - [Joomla 3 Component Security]
**Vulnerability:** Missing Authorization (Broken Access Control) in custom controller tasks (`process`, `doImport`).
**Learning:** Custom tasks in `JControllerForm` derivatives do not automatically inherit standard CRUD permissions. Explicit ACL checks (`authorise`) are mandatory to prevent unauthorized execution of business logic.
**Prevention:** Always verify user permissions at the start of any public or custom controller method, especially those performing sensitive operations like data import.

## 2025-05-23 - [Joomla 3 Component Security]
**Vulnerability:** Import features modifying user data to prevent CSV Injection on import (prepending `'`).
**Learning:** Preventing Formula Injection on *import* (write-time) permanently alters database data (e.g., user names starting with `@` become `'@`). This protects the admin export later, but corrupts the data for other uses (emails, integrations).
**Prevention:** Validation/Sanitization should ideally happen on *output* (read-time) or only invalid characters should be stripped, rather than escaping via modification, unless specifically required. However, for this project, the requirement was explicit to fail secure for CSV injection on import.
