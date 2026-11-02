# Authentication

SkillShare Hub now uses email and password authentication only. Registration stores passwords with PHP `password_hash()`, and login verifies them with `password_verify()`. Login and registration POSTs use the existing session-backed CSRF token helpers.

Public registration permits only the existing fresher and mentor roles; it does not accept admin registration. New mentor accounts are created inactive and require an administrator to review and enable them before mentor pages become available. Fresher accounts remain active after registration. Login redirects by the role loaded from the database. Protected pages re-check the account against the database, use the current database role for authorization, and reject missing or inactive accounts.

## Legacy OAuth data

OAuth login code, provider configuration, and the login buttons have been removed. The `oauth_accounts` table is intentionally not dropped or modified: the application no longer reads or writes it, but existing records remain in the database. Its schema files remain available for compatibility with existing database backups.

To remove the legacy table yourself, first make and verify a full database backup, inspect the row count, and confirm that no other application depends on it. The separate [optional cleanup SQL](../database/optional_cleanup_drop_oauth_accounts.sql) is non-destructive by default; it only drops the table if you manually uncomment the final statement. This permanently deletes the OAuth link records.

If Google, Facebook, GitHub, or redirect URI variables were configured in Apache or the server environment, remove those variables from the server configuration when convenient. They are no longer read by SkillShare Hub. Do not remove any unrelated user profile social-link fields.
