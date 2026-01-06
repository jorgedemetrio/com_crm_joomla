## 2024-05-23 - Stored XSS Prevention in Admin Views
**Vulnerability:** Raw output of database fields (`$item->id`) in administrator list views could potentially lead to Stored XSS if the database is compromised or if ID generation is manipulated.
**Learning:** Even fields that are seemingly safe (like IDs, which are UUIDs or Integers) should be escaped to enforce a 'Defense in Depth' strategy. This ensures that no matter what data ends up in the database, the view layer remains secure.
**Prevention:** Always use `$this->escape()` or `htmlspecialchars()` when outputting any variable in a view template, regardless of its expected content.

## 2024-05-23 - CSV Injection (Formula Injection) in Imports
**Vulnerability:** Importing CSV files without sanitization allows malicious users to inject payloads starting with `=`, `@`, `+`, `-`, `\t`, or `\r`. When these fields are later exported and opened in Excel, they can execute code (macros) or exfiltrate data.
**Learning:** Input sanitization is critical for data that might leave the system in different formats (like CSV export). Sanitizing on *import* ensures the database is clean, even if the Export feature is implemented later or by a different developer.
**Prevention:** Sanitize fields starting with dangerous characters by prepending a single quote `'`. Special care must be taken with `+` and `-` to avoid breaking legitimate phone numbers (use a regex whitelist for phone formats).
