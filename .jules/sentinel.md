## 2024-05-23 - Stored XSS Prevention in Admin Views
**Vulnerability:** Raw output of database fields (`$item->id`) in administrator list views could potentially lead to Stored XSS if the database is compromised or if ID generation is manipulated.
**Learning:** Even fields that are seemingly safe (like IDs, which are UUIDs or Integers) should be escaped to enforce a 'Defense in Depth' strategy. This ensures that no matter what data ends up in the database, the view layer remains secure.
**Prevention:** Always use `$this->escape()` or `htmlspecialchars()` when outputting any variable in a view template, regardless of its expected content.
