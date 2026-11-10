# Authentication

SkillShare Hub now uses email and password authentication only. Registration stores passwords with PHP `password_hash()`, and login verifies them with `password_verify()`. Login and registration POSTs use the existing session-backed CSRF token helpers.

Public registration permits only the existing fresher and mentor roles; it does not accept admin registration. New mentor accounts are created inactive and require an administrator to review and enable them before mentor pages become available. Fresher accounts remain active after registration. Login redirects by the role loaded from the database. Protected pages re-check the account against the database, use the current database role for authorization, and reject missing or inactive accounts.

## Password recovery

Forgot-password requests return the same message whether or not an account exists. Reset links use a 32-byte random token; only its SHA-256 hash is stored in the existing `users.reset_token` column. Links expire after 15 minutes and are cleared atomically after a successful password update. The reset page uses `password_hash()` and the existing login continues to verify passwords with `password_verify()`.

### Gmail SMTP setup (XAMPP)

1. Enable 2-Step Verification on the Gmail account, then create an App Password in the Google Account security settings. Use that App Password—not the normal Gmail password.
2. Copy `config/mail.local.php.example` to `config/mail.local.php` and enter the Gmail address, App Password, and the absolute application URL. For this XAMPP project the local URL is typically `http://localhost/SkillShare-Hub`; use the HTTPS site URL in production.
3. Keep `config/mail.local.php` private. It is ignored by Git. Alternatively, configure `SKILLSHARE_SMTP_USERNAME`, `SKILLSHARE_SMTP_APP_PASSWORD`, `SKILLSHARE_MAIL_FROM_NAME`, and `SKILLSHARE_APP_URL` in the server environment.
4. Ensure the PHP OpenSSL extension is enabled and outbound SMTP over port 587 is allowed. PHPMailer is loaded from `vendor/autoload.php` and uses Gmail SMTP with STARTTLS.

No reset email can be delivered until valid SMTP credentials and the correct application URL are configured. Never commit or share the App Password.

## Legacy OAuth data

OAuth login code, provider configuration, and the login buttons have been removed. The `oauth_accounts` table is intentionally not dropped or modified: the application no longer reads or writes it, but existing records remain in the database. Its schema files remain available for compatibility with existing database backups.

To remove the legacy table yourself, first make and verify a full database backup, inspect the row count, and confirm that no other application depends on it. The separate [optional cleanup SQL](../database/optional_cleanup_drop_oauth_accounts.sql) is non-destructive by default; it only drops the table if you manually uncomment the final statement. This permanently deletes the OAuth link records.

If Google, Facebook, GitHub, or redirect URI variables were configured in Apache or the server environment, remove those variables from the server configuration when convenient. They are no longer read by SkillShare Hub. Do not remove any unrelated user profile social-link fields.
